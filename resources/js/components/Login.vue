<template>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header">Login</div>
                  <div class="card-body">
                    <div class="alert alert-danger mt-2" role="alert" v-if="error">
                        {{ error }}
                    </div>
                      <form>
                        <div class="row mb-3">
                              <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>

                              <div class="col-md-6">
                                  <input id="email" type="email" class="form-control" name="email" required autocomplete="off" autofocus v-model="user.email">
                              </div>
                          </div>

                          <div class="row mb-3">
                              <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                              <div class="col-md-6">
                                  <input id="password" type="password" class="form-control" required autocomplete="off" v-model="user.password">
                              </div>
                          </div>

                          <div class="row mb-0">
                              <div class="col-md-8 offset-md-4">
                                  <button type="submit" class="btn btn-primary" :disabled="isProcessing" @click="handleSubmit">
                                      Login
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
        email: "",
        password: ""
      },
      isProcessing: false,
      error: ""
    }
  },
  methods: {
    handleSubmit(e) {
      let { user, $router, validateLogin } = this
      e.preventDefault()
      this.isProcessing = true
      axios.get('sanctum/csrf-cookie').then(response => {
        axios.post('api/login', user)
        .then(response => {
            if (response.data.response) {
                window.location.href = '/'
            } else {
                this.error = response.data.message
            }
        })
        .catch(function (error) {
            console.error(error);
        });
      })
      this.isProcessing = false
    },
  },
  beforeRouteEnter(to, from, next) {
      if (window.Laravel.isLoggedin) {
          return next('/');
      }
      next();
  }
}
</script>