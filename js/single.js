var game = new Vue({
  el: '#section',
  data: {
    //数据绑定
    gamename: "世界你好",
    catname: "游戏",
    getCatUrl: 'goback',
    gamecontent: "123",
    siteurl: "https://www.linuxgame.cn",
  },
  methods: {
    //数据绑定
    goBack: function () {
      console.log(this.getCatUrl);
    },
    getSiteUrl: function () {
      let wwwpath = window.location.href;
      let pathname = window.location.pathname;
      let pos = wwwpath.indexOf(pathname);
      return wwwpath.substring(0, pos);
    },
    getPostJson: function(url, callback) {
      axios({
        method: 'get',
        url: url,
        responseType: 'json',
      }).then(function(response) {
          callback(response.data);
        });
    }
  },
  mounted: function() {
    this.siteurl = this.getSiteUrl();
    const that = this;
    that.getPostJson(that.siteurl + '/wp-json/wp/v2/posts/1',function(data){
      console.log(data);
    });
  },
})
