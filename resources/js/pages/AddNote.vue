<template>
    <div class="container">
        <h4>New Note</h4>
        <div class="row mt-5">
            <div class="col-md-12">
              <div class="alert alert-danger mt-2" role="alert" v-if="error">
                  {{ error }}
              </div>
              <div class="alert alert-success my-2" role="alert" v-if="success">
                  {{ success }}
              </div>
                <form @submit.prevent="addNote">
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
                    <button type="submit" class="btn btn-primary mt-5" :disabled="isProcessing">Add Note</button>
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
      note: {
        title: "",
        body: "",
        file_attachments: ""
      },
      image: "",
      isProcessing: false,
      error: "",
      success: "",
      files: [],
      activeFile: "",
    }
  },
  computed: {
    fileString() {
      return this.files.toString()
    },
  },
  mounted() {
    this.resetFileInput()
  },
  methods: {
    addNote() {
      let { note, validateNote, fileString } = this
      this.isProcessing = true
      this.error = ""
      if (validateNote()) {
        axios.get('/sanctum/csrf-cookie').then(response => {
          axios.post('/api/notes/add', {
            ...note,
            file_attachments: fileString
          })
          .then(response => {
            if (response.data.response) {
              this.$router.push({name: 'notes'})
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
