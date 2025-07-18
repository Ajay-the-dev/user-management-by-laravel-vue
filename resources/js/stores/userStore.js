import { defineStore } from "pinia"

export const useUserStore = defineStore('user', {
  state: () => ({
    username: null,
    name: null,
    email: null,
    allData: {},
    isLoggedIn: false
  }),

  actions: {
    setUser(user = '') {
      if (user) {
        this.username = user.username
        this.name = user.name
        this.email = user.email
        this.allData = user
      } else {
        this.username = null
        this.name = null
        this.email = null
        this.allData = {}
      }
    },

    setLoggedIn(user) {
      this.setUser(user)
      this.isLoggedIn = true
    },

    logout() {
      this.setUser('')
      this.isLoggedIn = false
    }
  },

  getters: {
    fullName: (state) => `${state.name}`,
    userEmail: (state) => `${state.email}`,
    userUsername: (state) => `${state.username}`,
  },

  persist: true
})
