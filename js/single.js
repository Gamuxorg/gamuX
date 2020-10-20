var game = new Vue({
  el: '#section',
  data: {
    //数据绑定
    postname: "游戏名加载中...",
    catname: "游戏分类加载中...",
    caturl: 'goback',
    postcontent: "游戏内容加载中...",
    siteurl: "https://www.linuxgame.cn",
    cururl: "",
    postdate: "",
    imgtype: "",
    buyurls: [
      {text: "在Steam购买本游戏", url: "http://www.baidu.com"},
      {text: "在GOG购买本游戏", url: "http://www.360.cn"}
    ],
  },
  methods: {
    //数据绑定
    goBack: function () {
      window.location.href=this.caturl; 
    },
    getSiteUrl: function() {
      let wwwpath = window.location.href;
      let pathname = window.location.pathname;
      let pos = wwwpath.indexOf(pathname);
      this.siteurl = wwwpath.substring(0, pos);
      this.cururl = wwwpath;
    },
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
    this.getSiteUrl();
    urljson = await this.getPostJson(this.cururl);
    postid = urljson.headers.link.split(/[,;=>]/)[5];
    urldata = await this.getPostJson(this.siteurl + "/wp-json/wp/v2/posts/" + postid+"?_embed");
    postdata = urldata.data;
    console.log(postdata);
    postterm = postdata["_embedded"]["wp:term"][0];
    this.catname = postterm[0].name;
    this.caturl = postterm[0].link;
    this.postname = postdata.title.rendered;
    this.postcontent = postdata.content.rendered;
    this.postdate = postdata.date.split("T")[0];
  },
})
