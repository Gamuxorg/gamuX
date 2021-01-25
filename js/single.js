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
    postslide: ['1'],
    siteurl: "https://www.linuxgame.cn",
    cururl: "",
    postid: 1,
    editurl: "",
    contributeurl: "",
    postdate: "",
    imgtype: "",
    thumbnail: "",
    //当前登录用户名
    username: "",
    useravatar: "",
    //当前登录用户id
    userid: "",
    //当前登录用户url
    userurl: "",
    //当前时间,gmt
    curdate: "",
    //当前时间,本地
    curutcdate: "",
    taglist: "",
    islogin: 0,
    buyurls: [],
    posteditcount: 0,
    activities: [{
      author: '',
      content: ' · 最后作者',
      timestamp: '2012-08-20',
      type: 'primary',
      icon: 'el-icon-refresh',
      size: 'large',

    }, {
      author: '',
      content: '',
      timestamp: '感谢各位的贡献',
      icon: 'el-icon-magic-stick',
      type: 'info',
      size: 'large'
    }, {
      author: '',
      content: ' · 开始创建',
      timestamp: '2012-08-20',
      icon: 'el-icon-edit',
      type: 'info',
      size: 'large'
    }],
    comments: [],
    comnum: 0,
    parentId: 0,
    rootparent: 0,
    //comment editor
    editorContent: '',
    editorOption: {
      modules: {
        toolbar: 
          ['bold', 'underline', 'strike', 'blockquote', 'code-block', {'color': []}, 'clean'],
      },
      placeholder: '',
    },
    replyTextarea: '',
    replyModJson: [],
    downloadlist: [],
    dialogdownload: false,
  },
  methods: {
    clickDownload: async function(a) {
      axios({
        method: 'get',
        url: "https://kr.linuxgame.cn:8088/download_counting.php",
        responseType: 'json',
        params: {
          post_id: this.postid,
        }
      });
      window.open(a);
    },
    dialogloginVisible: function() {
      gamux.$refs.login.$el.click();
    },
    //数据绑定
    goBack: function () {
      window.location.href=this.siteurl + "/gamelist"; 
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
    onEditorChange: function({quill, html, text}) {
      this.editorContent = html;
    },
    onEditorButtonClicked: function (e) {
      let content;
      if (e.currentTarget.id == "reportButton") {
        content = this.editorContent;
        this.parentId = 0;
      }
      else if (e.currentTarget.id == "replyButton") {
        content = this.replyTextarea;
      }
      else {
        alert("非法点击!");
      }
      var that = this;
      axios({
        method: 'POST',
        url: that.siteurl + '/wp-json/wp/v2/comments',
        data: {
          'content': content,
          'post': that.postid,
          'author_name': that.username,
          'author': that.userid,
          'date': that.curdate,
          'date_gmt': that.curutcdate,
          'parent': that.parentId,
        },
      }).then(function(response) {

        const thedate = that.curdate.split("T")[0] + " " + that.curdate.split("T")[1];
        var itemlist = {
          'id': response.data.id,
          'content': content,
          'post': that.postid,
          'author_name': that.username,
          'children': [],
          'author': that.userid,
          'date': thedate,
          'parent': that.parentId,
          'author_avatar': that.useravatar,
        }
        if(that.comments.length == 0) {
          that.comnum = 1;
        }
        if ( that.parentId == 0 ) {
          that.comments.unshift(itemlist);
          that.$set(that.comments, 0, itemlist);
        }
        else {
          for (var itemi = 0; itemi < game.comments.length; itemi++) {
            if (game["comments"][itemi]["id"] == game.rootparent) {
              var item = game["comments"][itemi]["children"];
              item.unshift(itemlist);
              Vue.set(game["comments"], itemi, game["comments"][itemi]);
              break;
            }
          }
        }
      }).catch(function(e) {
        alert("评论失败，出错！请检查您的评论是否为空。");
        console.log(e);
      });
      this.$nextTick(function () {
        this.editor.setContents([{ insert: '\n' }]);
        const reply = document.getElementById("reply");
        reply.style.display= 'none';
        this.replyTextarea = "";
      });
    },
    commentReply: function (e) {
      var a = document.getElementById("reply");
      a.remove();
      var inserted =  e.currentTarget.parentNode.parentNode;
      inserted.insertBefore(a,inserted.childNodes[-1]);
      a.style.display= 'block';
      this.replyTextarea = "";
      this.$nextTick(function () {
        var replyaria = document.getElementById("reply");
        var wholeid = replyaria.parentNode.parentNode.parentNode.parentNode.parentNode.id;
        var iddata = wholeid.split("-");
        if (iddata[1] == "reply" ) {
          this.parentId = iddata[3];
          this.rootparent = iddata[2];
        }
        else if ( iddata[1] == "main" ) {
          this.parentId = iddata[2];
          this.rootparent = iddata[2];          
        }
        else {
          alert("内部错误！");
        }
      });
    },
    commentReplyCancle: function () {
      const a = document.getElementById("reply");
      a.style.display= 'none';
      this.replyTextarea = "";
    },
  },
  components: {
    'carousel-3d': Carousel3d.Carousel3d,
    'slide': Carousel3d.Slide
  },
  mounted: async function() {
    this.siteurl = gamux.siteurl;
    this.cururl = gamux.cururl;
    //文章详情
    const urljson = await this.getPostJson(this.cururl);
    const postid = urljson.headers.link.split(/[,;=>]/)[11];
    this.postid = postid;
    this.editurl = this.siteurl + "/wp-admin/post.php?post=" + postid + "postid'&action=edit";
    this.contributeurl = this.siteurl + "/wp-admin/post-new.php";
    const postdataorigin = await this.getPostJson(this.siteurl + "/wp-json/wp/v2/posts/" + postid);
    const postdata = postdataorigin.data;
    const postterm = postdata.exts.categories;
    this.catname = postterm[0]["name"];
//    this.caturl = postterm[0].link;
    this.postname = postdata.title.rendered;
    this.postcontent = postdata.exts.content.body;
    this.postslide = postdata.exts.content.slides;

    //下载链接
    this.downloadlist = postdata.exts.downloadList;
    //购买链接
    if (postdata.exts.buyUrls[0] == null) {
      this.buyurls = null;
    }
    else {
      this.buyurls = postdata.exts.buyUrls;
    }

    //文章更新记录
    this.postdate = postdata.date.split("T")[0];
    const modifieddate = postdata.modified.split("T")[0];
    this.activities[2].timestamp = this.postdate;
    this.activities[0].timestamp = modifieddate;
    this.activities[2].author = postdata.exts.authorName;
    this.activities[0].author = postdata.exts.modAuthorName;
    this.activities[1].content = '期间一共更新了' + postdata["exts"]["editHistorys"]["count"] + '次';
    this.thumbnail = postdata.exts.thumbnail;

    //文章标签
    this.taglist = postdata.exts.tagList ? postdata.exts.tagList : "无标签";  //空时后端返回false

    //日期    
    const getDate = new Date();
    const tmpcurdate = new Date(getDate.getTime() - (getDate.getTimezoneOffset() * 60000)).toJSON();
    const tmpcurutcdate = getDate.toJSON();
    this.curdate = tmpcurdate.split(".")[0];
    this.curutcdate = tmpcurutcdate.split(".")[0]; 

    //用户信息
    this.islogin = gamux.islogin;
    this.username = gamux.username;
    this.userid = gamux.userid;
    this.useravatar = gamux.useravatar;

    //评论
    const comment = await this.getPostJson("/wp-json/gamux/v1/comments/" + postid);
    this.comments = comment.data;
    this.comnum = comment.data.length;

    //css
  },
  computed: {
    editor: function() {
      return this.$refs.myQuillEditor.quill;
    },
  },
})
