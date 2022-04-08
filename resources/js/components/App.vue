<template>
  <div class="container">
      <div class="text-center" style="margin: 20px 0px 20px 0px;">
          <img src="https://avatars.githubusercontent.com/u/29140663?s=200"><br>
          <span class="text-secondary">John Carlo De Leon</span>
      </div>

      <nav class="navbar navbar-expand-lg navbar-light" style="background-color: rgb(227, 242, 253);">
          <div class="collapse navbar-collapse">
              <!-- for logged-in user-->
              <div class="navbar-nav">
                  <template v-if="isLoggedIn">
                    <router-link to="/" class="nav-item nav-link">My Notes</router-link>
                    <router-link to="/shared-notes" class="nav-item nav-link">Shared Notes</router-link>
                    <a class="nav-item nav-link" style="cursor: pointer;" @click="logout">Logout</a>
                  </template>
                  <template v-else>
                    <router-link to="/login" class="nav-item nav-link">Login</router-link>
                    <router-link to="/register" class="nav-item nav-link">Register</router-link>
                  </template>
              </div>
          </div>
      </nav>
      <br/>
      <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
     return {
        isLoggedIn: false,
     }
   },
   created() {
      if (window.Laravel.isLoggedin) {
        this.isLoggedIn = true
      }
   },
   methods: {
     logout(e) {
        e.preventDefault()
        axios.get('/sanctum/csrf-cookie').then(response => {
            axios.post('/api/logout')
                .then(response => {
                    if (response.data.response) {
                        window.location.href = "/login"
                    } else {
                        console.log(response)
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
        })
      }
   }
}
</script>