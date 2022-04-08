<template>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header">Register</div>
                  <div class="card-body">
                    <div class="alert alert-danger mt-2" role="alert" v-if="error">
                        {{ error }}
                    </div>
                      <form>
                          <div class="row mb-3">
                              <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

                              <div class="col-md-6">
                                  <input id="name" type="text" class="form-control" required autocomplete="off" autofocus v-model="user.name">
                              </div>
                          </div>

                          <div class="row mb-3">
                              <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>

                              <div class="col-md-6">
                                  <input id="email" type="email" class="form-control" name="email" required autocomplete="off" v-model="user.email">
                              </div>
                          </div>

                          <div class="row mb-3">
                              <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                              <div class="col-md-6">
                                  <input id="password" type="password" class="form-control" required autocomplete="off" v-model="user.password">
                              </div>
                          </div>

                          <div class="row mb-3">
                              <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirm Password</label>

                              <div class="col-md-6">
                                  <input id="password-confirm" type="password" class="form-control" required autocomplete="off" v-model="user.password_confirmation">
                              </div>
                          </div>

                          <div class="row mb-0">
                              <div class="col-md-6 offset-md-4">
                                  <button type="submit" class="btn btn-primary" @click="handleSubmit" :disabled="isProcessing">
                                      Register
                                  </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      },
      isProcessing: false,
      error: ""
    }
  },
  methods: {
    handleSubmit(e) {
      e.preventDefault()
      let { user, $router } = this
      if (user.email && user.name && user.password) {
          axios.get('/sanctum/csrf-cookie').then(response => {
              axios.post('api/register', 
              user)
              .then(response => {
                  if (response.data.response) {
                      $router.push("/login")
                  } else {
                      this.error = response.data.message
                  }
              })
              .catch(function (error) {
                  console.error(error);
              });
          })
      }
    }
  },
  beforeRouteEnter(to, from, next) {
      if (window.Laravel.isLoggedin) {
          return next('/');
      }
      next();
  }
}
</script>