axios.defaults.headers.post['X-WP-Nonce'] = wpApiSettings.nonce;

var gamux = new Vue ({
  el: '#header',
  data: {
    islogin: 0,
    username: "",
    userid: "",
    useravatar: "",
    dialogloginVisible: false,
    siteurl: "",
    cururl: "",
    logout: "",
  },
  methods: {
    openUnreadComment: function(url,num) {
      this.$notify.info({
        title: '未读评论',
        duration: 3000,
        message: '您有' + num + '条未读评论，点击查看',
        onClick: function() {
          window.location.href = url;
        }
      });
    },
    getPostJson: async function(url) {
      let a = await axios({
        method: 'get',
        url: url,
        responseType: 'json',
      });
      return a;
    },
    getSiteUrl: function() {
      let wwwpath = window.location.href;
      let pathname = window.location.pathname;
      let pos = wwwpath.indexOf(pathname);
      this.siteurl = wwwpath.substring(0, pos);
      this.cururl = wwwpath;
    },
  },
  mounted: async function() {
    this.getSiteUrl();
    try {
      const userinfos = await this.getPostJson(this.siteurl + "/wp-json/wp/v2/users/me?_wpnonce=" + wpApiSettings.nonce);
      const userinfo = userinfos.data;
      this.username = userinfo.name;
      this.userid = userinfo.id;
      this.islogin = 1;
      this.useravatar = userinfo.avatar;
      this.logout = decodeURIComponent(userinfo.logout_url);
    }
    catch (error) {
      this.islogin = 0;
      console.log("未登录！");
    }
    this.$nextTick(async function() {
      if (this.islogin == 1 && this.cururl == this.siteurl) {
        const userunread = await this.getPostJson(this.siteurl + "/wp-json/gamux/v1/comments/unread/" + this.userid);
        if (userunread.data.unread > 0) {
          this.openUnreadComment(userunread.data.redirect_url, userunread.data.unread);
        }
      }
    });
  },
});