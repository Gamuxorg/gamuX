var game = new Vue({
  el: '#section',
  data: {
    //数据绑定
    postname: "游戏名加载中...",
    catname: "游戏分类加载中...",
    modifiedauthor: "gamux",
    caturl: 'goback',
    postcontent: "游戏内容加载中...",
    siteurl: "https://www.linuxgame.cn",
    cururl: "",
    postdate: "",
    imgtype: "",
    thumbnail: "",
    comments: "",
    islogin: false,
    buyurls: [
      {text: "在Steam购买本游戏", url: "https://www.baidu.com"},
      {text: "在GOG购买本游戏", url: "https://www.360.cn"}
    ],
    activities: [{
      author: 'gamux',
      content: '进行了更新',
      timestamp: '2012-08-20',
      type: 'primary',
      icon: 'el-icon-refresh',
      size: 'large',

    }, {
      author: 'gamux',
      content: '创建了文章',
      timestamp: '2012-08-20',
      icon: 'el-icon-edit',
      type: 'info',
      size: 'large'
    }],
    tableData: [{
      version: '1.0.2',
      date: '2016-05-02',
      quantity: 35,
      volume: '3.5G',
      link: 'https://www.baidu.com',
      remark: '上海市'
    },{
      version: '1.0.2',
      date: '2016-05-02',
      quantity: 35,
      volume: '3.5G',
      link: 'https://www.baidu.com',
      remark: '路 1518 弄'
    },],
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
    const urljson = await this.getPostJson(this.cururl);
    const postid = urljson.headers.link.split(/[,;=>]/)[5];
    const postdataorigin = await this.getPostJson(this.siteurl + "/wp-json/wp/v2/posts/" + postid+"?_embed");
    const postdata = postdataorigin.data;
    const postterm = postdata["_embedded"]["wp:term"][0];
    this.catname = postterm[0].name;
    this.caturl = postterm[0].link;
    this.postname = postdata.title.rendered;
    this.postcontent = postdata.content.rendered;
    this.postdate = postdata.date.split("T")[0];
    const modifieddate = postdata.modified.split("T")[0];
    this.activities[1].timestamp = this.postdate;
    this.activities[0].timestamp = modifieddate;
    this.thumbnail = postdata.exts.thumbnail;
    this.islogin = postdata.exts.isUserLogin;
    const commentdataorigin = await this.getPostJson("/wp-json/wp/v2/comments?post=" + postid);
    const commentdata = commentdataorigin.data;
    this.comments = commentdata;
    console.log(commentdata); 
  },
})
