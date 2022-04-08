<template>
    <div class="container">
        <h4>Note: {{ noteTitle }}</h4>
        <small>Updated By: {{ note.updated_by_name }}</small><br/>
        <small v-if="note.user_id !== authUser.id">Shared To: {{ mappedSharedListNames.toString() }}</small>
        <div class="row mt-5">
            <div class="col-md-12">
              <div class="alert alert-danger mt-2" role="alert" v-if="error">
                  {{ error }}
              </div>
              <div class="alert alert-success my-2" role="alert" v-if="success">
                  {{ success }}
              </div>
                <form @submit.prevent="updateNote">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" v-model="note.title">
                    </div>
                    <div class="form-group mt-3">
                        <label>Body</label>
                        <vue-editor
                          id="editor"
                          v-model="note.body"
                        >
                        </vue-editor>
                    </div>
                    <div class="form-group mt-3">
                        <label>Attach Files</label>
                        <div class="input-group">
                          <input type="file" class="form-control" @change="getFile" ref="fileInput">
                          <div class="input-group-append">
                            <button class="btn btn-primary" type="button" @click="attachFile" :disabled="!activeFile">Attach</button>
                          </div>
                        </div>
                    </div>
                    <div class="row" v-if="files.length">
                      <div class="form-group mt-3 col-md-6">
                        <div 
                          class="input-group mb-3"
                          v-for="(file, index) in files"
                          :key="index"
                        >
                          <input type="text" class="form-control" :value="file" disabled>
                          <div class="input-group-append">
                            <a class="btn btn-info" type="button" :href="file" :download="file" target="_blank" :disabled="isProcessing">Download</a>
                            <button class="btn btn-danger" type="button" @click="deleteAttachment(index)" :disabled="isProcessing">Delete</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <template v-if="note.user_id == authUser.id">
                      <div class="form-group mt-3">
                          <label>Share To</label>
                          <select 
                            v-model="note.share" 
                            class="form-control"
                            @change="updateSharedUserList"
                          >
                            <option value="">Select user to share</option>
                            <option 
                              v-for="(user, index) in sharedList" 
                              :key="index" 
                              :value="`${user.id}:${user.name}`"
                            >
                              {{user.name}}
                            </option>
                          </select>
                      </div>
                      <div class="row" v-if="sharedUserList.length">
                        <div class="form-group mt-3 col-md-6">
                          <div 
                            class="input-group mb-3"
                            v-for="(user, index) in sharedUserList"
                            :key="index"
                          >
                            <input type="text" class="form-control" :value="user.name" disabled>
                            <div class="input-group-append">
                              <button class="btn btn-danger" type="button" @click="deleteSharedUser(index)">Delete</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                    <button type="submit" class="btn btn-primary mt-5" :disabled="isProcessing">Update Note</button>
                    <router-link to="/" class="btn btn-danger mt-5" :disabled="isProcessing">Cancel</router-link>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { VueEditor } from "vue2-editor";

export default {
  name: 'addNote',
  components: { VueEditor },
  data() {
    return {
      noteTitle: "",
      note: { 
        title: '',
        body: '',
        share: ''
      },
      users: [ ],
      sharedUserList: [ ],
      isProcessing: false,
      error: "",
      success: "",
      authUser: { },
      files: [],
      activeFile: null,
    }
  },
  computed: {
    mappedSharedList() {
      let { sharedUserList } = this
      return sharedUserList.map(list => list.user_id)
    },
    mappedSharedListNames() {
      let { sharedUserList } = this
      return sharedUserList.map(list => list.name)
    },
    sharedList() {
      let { sharedUserList, users, mappedSharedList } = this
      return users.filter(user => !mappedSharedList.includes(user.id))
    },
    fileString() {
      return this.files.toString()
    },
  },
  created() {
    axios.get('/sanctum/csrf-cookie').then(response => {
      this.getNote()
      this.getUserList()
      this.getSharedUserList()
    })
    
    this.authUser = window.Laravel.user
  },
  mounted() {
    this.resetFileInput()
  },
  methods: {
    getNote() {
      axios.get(`/api/notes/edit/${this.$route.params.id}`)
      .then(response => {
          if (response.data.response) {
            this.note = response.data.data
            this.note.share = ""
            this.noteTitle = this.note.title
            this.files = this.note.file_attachments ? this.note.file_attachments.split(",") : []
          }
          else {
            this.$router.push("/")
          }
      })
      .catch(function (error) {
          console.error(error);
      });
    },
    getUserList() {
      axios.get('/api/users/list')
      .then(response => {
          if (response.data.response) {
            this.users = response.data.data
          }
      })
      .catch(function (error) {
          console.error(error);
      });
    },
    getSharedUserList() {
      axios.post('/api/notes/shared/users/list', {
        note_id: this.$route.params.id
      })
      .then(response => {
          if (response.data.response) {
            this.sharedUserList = response.data.data
          }
      })
      .catch(function (error) {
          console.error(error);
      });
    },
    updateNote() {
      let { note, mappedSharedList, validateNote, fileString } = this
      this.isProcessing = true
      this.error = ""
      if (validateNote()) {
        axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post(`/api/notes/update/${this.$route.params.id}`, 
          {
            ...note,
            share: mappedSharedList,
            file_attachments: fileString
          }
        )
        .then(response => {
          if (response.data.response) {
            this.success = response.data.message
          }
          else {
            this.error = response.data.message
          }
        })
        .catch(function (error) {
            console.warn(error);
            this.error = error
        });
      })
      }
      this.isProcessing = false
    },
    validateNote() {
      let checker = false
      let { note } = this
      
      if (note.title == "") {
        this.error = "The title field is required."
      }
      
      if (this.error) 
        return false
      else
        return true
    },
    updateSharedUserList() {
      let { note, sharedUserList } = this
      if (note.share) {
        let user = note.share.split(':')
        sharedUserList.push({user_id: parseInt(user[0]), name: user[1]})
        note.share = ""
      }
    },
    deleteSharedUser(index) {
      this.sharedUserList.splice(index, 1)
    },
    getFile(e) {
      this.activeFile = e.target.files[0]
    },
    attachFile() {
      let formData = new FormData();
      formData.append("image", this.activeFile);
      
      this.isProcessing = true
      axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post('/api/uploads/file', formData)
        .then(response => {
          if (response.data.response)
            this.files.push(response.data.data)
        })
        .catch(err => {
          console.log(err);
        });
      })
      this.resetFileInput()
      this.isProcessing = false
    },
    deleteAttachment(index) {
      this.files.splice(index, 1)
    },
    resetFileInput() {
      this.$refs.fileInput.value = null;
      this.activeFile = ""
    }
  },
  beforeRouteEnter(to, from, next) {
      if (!window.Laravel.isLoggedin) {
          return next('/login');
      }
      next();
  }
}
</script>
