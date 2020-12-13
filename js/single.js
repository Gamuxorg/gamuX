Vue.use(window.VueQuillEditor);
axios.defaults.headers.post['X-WP-Nonce'] = wpApiSettings.nonce;

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
    postid: 1,
    editurl: "",
    contributeurl: "",
    postdate: "",
    imgtype: "",
    thumbnail: "",
    comments: [],
    comnum: 0,
    //当前登录用户名
    username: "",
    //当前登录用户id
    userid: "",
    //当前登录用户url
    userurl: "",
    //当前时间,gmt
    curdate: "",
    //当前时间,本地
    curutcdate: "",
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
    //comment editor
    editorContent: '',
    editorOption: {
      modules: {
        toolbar: 
          ['bold', 'underline', 'strike', 'blockquote', 'code-block', {'color': []}, 'clean'],
      },
      placeholder: '',
    },    
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
    //comment editor
    onEditorBlur: function(quill) {
      console.log('editor blur!', quill)
    },
    onEditorFocus: function(quill) {
      console.log('editor focus!', quill)
    },
    onEditorReady: function(quill) {
      console.log('editor ready!', quill)
    },
    onEditorChange: function({quill, html, text}) {
      this.editorContent = html;
    },
    onEditorButtonClicked: function () {
      axios({
        method: 'post',
        url: this.siteurl + '/wp-json/wp/v2/comments',
        data: {
          'content': this.editorContent,
          'post': this.postid,
          'author_name': this.username,
          'author': this.userid,
          'date': this.curdate,
          'date_gmt': this.curutcdate,
        },
      }).then(function(response) {
        console.log(response);
      }).catch(function(e) {
        console.log(e);
        console.log('失败');
      });
    }
  },
  mounted: async function() {
    const siteurl = this.getSiteUrl();
    //文章详情
    const urljson = await this.getPostJson(this.cururl);
    const postid = urljson.headers.link.split(/[,;=>]/)[5];
    this.postid = postid;
    this.editurl = siteurl + "/wp-admin/post.php?post=" + postid + "postid'&action=edit";
    this.contributeurl = siteurl + "/wp-admin/post-new.php";
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
//日期    
    const getDate = new Date();
    this.curdate = new Date(getDate.getTime() - (getDate.getTimezoneOffset() * 60000)).toJSON();
    this.curutcdate = getDate.toJSON();
    //userinfo
    const userinfos = await this.getPostJson("/wp-json/wp/v2/users/me?_wpnonce=" + wpApiSettings.nonce);
    const userinfo = userinfos.data;
    if (userinfos.statusText == "OK") {
      this.username = userinfo.name;
      this.userid = userinfo.id;
    }
    else {
      console.log(userinfos);
    }
    //comment
    const comment = await this.getPostJson("/wp-json/gamux/v1/comments/" + postid);
    this.comments = comment.data;
    this.comnum = comment.data.length;
  },
  computed: {
    editor: function() {
      return this.$refs.myQuillEditor.quill;
    }
  },
})
