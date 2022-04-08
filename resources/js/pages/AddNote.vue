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
                          useCustomImageHandler
                          @imageAdded="handleImageAdded"
                          v-model="note.body"
                        >
                        </vue-editor>
                    </div>
                    <button type="submit" class="btn btn-primary mt-5" :disable="isProcessing">Add Note</button>
                    <router-link to="/" class="btn btn-danger mt-5" :disable="isProcessing">Cancel</router-link>
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
        body: ""
      },
      isProcessing: false,
      error: ""
    }
  },
  methods: {
    addNote() {
      let { note } = this
      this.isProcessing = true
      this.error = ""
      axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post('/api/notes/add', note)
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
      this.isProcessing = false
    },
    handleImageAdded() {
      
    }
  }
}
</script>
