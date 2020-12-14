axios.defaults.headers.post['X-WP-Nonce'] = wpApiSettings.nonce;

var gamux = new Vue ({
  el: '#header',
  data: {
    islogin: 0,
    username: "",
    userid: "",
  },
  methods: {
    getPostJson: async function(url) {
      let a = await axios({
        method: 'get',
        url: url,
        responseType: 'json',
      });
      return a;
    },
  },
  mounted: async function() {
    try {
      const userinfos = await this.getPostJson("/wp-json/wp/v2/users/me?_wpnonce=" + wpApiSettings.nonce);
      const userinfo = userinfos.data;
      this.username = userinfo.name;
      this.userid = userinfo.id;
      this.islogin = 1;
    }
    catch (error) {
      this.islogin = 0;
      console.log("未登录！");
    }
  },
});