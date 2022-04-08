<template>
    <div class="container">
      <div class="row mb-2">
        <div class="col-md-6">
          <h3>My Notes</h3>
        </div>
        <div class="col-md-6 text-end">
          <router-link to="/add-note" class="btn btn-primary">Add Note</router-link>
        </div>
      </div>
      <div class="alert alert-danger my-2" role="alert" v-if="error">
          {{ error }}
      </div>
      <div class="alert alert-success my-2" role="alert" v-if="success">
          {{ success }}
      </div>
      <table class="table table-bordered">
          <thead>
          <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="note in notes" :key="note.id">
              <td>{{ note.id }}</td>
              <td>{{ note.title }}</td>
              <td>
                  <div class="btn-group" role="group">
                      <router-link :to="{name: 'editNote', params: { id: note.id }}" class="btn btn-primary">Edit
                      </router-link>
                      <button class="btn btn-danger" @click="deletePost(note.id)">Delete</button>
                  </div>
              </td>
          </tr>
          </tbody>
      </table>
    </div>
</template>
 
<script>
    export default {
        data() {
            return {
                notes: [],
                error: "",
                success: ""
            }
        },
        created() {
          this.axios
            .get('/api/notes')
            .then(response => {
                this.notes = response.data.data;
            });
        },
        methods: {
          deletePost(id) {
            this.axios
            .delete(`/api/notes/delete/${id}`)
            .then(response => {
                if (response.data.response) {
                  let i = this.notes.map(note => note.id).indexOf(id); 
                  this.notes.splice(i, 1)
                  this.success = response.data.message
                }
                else {
                  this.error = response.data.message
                }
            });
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