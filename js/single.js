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
    parent_id: 0,
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
    downloadlist: [],
    dialogdownload: false,
  },
  methods: {
    dialogloginVisible: function() {
      gamux.$refs.login.$el.click();
    },
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
    },
    onEditorFocus: function(quill) {
    },
    onEditorReady: function(quill) {
    },
    onEditorChange: function({quill, html, text}) {
      this.editorContent = html;
    },
    onEditorButtonClicked: function (e,content) {
      console.log(this.curdate, this.curutcdate);
      if (e.currentTarget.id == "reportButton") {
        content = this.editorContent;
        this.parent_id = 0;
      }
      else if (e.currentTarget.id == "replyButton") {
        content = this.replyTextarea;
      }
      else {
        alert("非法点击!");
      }
      axios({
        method: 'POST',
        url: this.siteurl + '/wp-json/wp/v2/comments',
        data: {
          'content': content,
          'post': this.postid,
          'author_name': this.username,
          'author': this.userid,
          'date': this.curdate,
          'date_gmt': this.curutcdate,
          'parent': this.parent_id,
        },
      }).then(function(response) {
        game.comnum = 0;
        game.$nextTick(function () {
          game.comnum = 1;
        });
        console.log(response);
/*
        let item;
        const thisdate = game.curdate.split("T")[0] + " " + game.curdate.split("T")[1];
        if ( game.parent_id == 0 ) {
          item = game.comments;
        }
        else {
          for (const i=0; i < game.comments.length; i++) {
            if (game["comments"][i]["id"] == game.parent_id) {
              if ( (game["comments"][i]["children"]).length == 0 ) {
                console.log((game["comments"][i]["children"]).length);
                game["comments"][i]["children"] = [];
                item = game["comments"][i]["children"];
              }
              else {
                item = game["comments"][i]["children"];
              }
              break;
            }
          }
        }

        const itemLen = item.length;
        const itemlist = {
          'id': response.data.id,
          'content': content,
          'post': game.postid,
          'author_name': game.username,
          'author': game.userid,
          'date': thisdate,
          'parent': game.parent_id,
          'author_avatar': this.useravatar,      
        }
        Vue.set(item,itemLen,itemlist);
        console.log(item); 
*/
      }).catch(function(e) {
        alert("评论失败，出错！" + e);
        console.log(e);
      });
      this.editor.setContents([{ insert: '\n' }]);
      const a = document.getElementById("reply");
      a.style.display= 'none';
      this.replyTextarea = "";
    },
    commentReply: function (e) {
      const a = document.getElementById("reply");
      a.remove();
      const inserted =  e.currentTarget.parentNode.parentNode;
      inserted.insertBefore(a,inserted.childNodes[-1]);
      a.style.display= 'block';
      this.replyTextarea = "";
      this.$nextTick(function () {
        const replyaria = document.getElementById("reply");
        const wholeid = replyaria.parentNode.parentNode.parentNode.parentNode.parentNode.id;
        const iddata = wholeid.split("-");
        if (iddata[1] == "reply" || iddata[1] == "main") {
          this.parent_id = iddata[2];
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
  mounted: async function() {
    const siteurl = this.getSiteUrl();
    //文章详情
    const urljson = await this.getPostJson(this.cururl);
    const postid = urljson.headers.link.split(/[,;=>]/)[5];
    this.postid = postid;
    this.editurl = this.siteurl + "/wp-admin/post.php?post=" + postid + "postid'&action=edit";
    this.contributeurl = this.siteurl + "/wp-admin/post-new.php";
    const postdataorigin = await this.getPostJson(this.siteurl + "/wp-json/wp/v2/posts/" + postid+"?_embed");
    const postdata = postdataorigin.data;
    const postterm = postdata["_embedded"]["wp:term"][0];
    this.catname = postterm[0].name;
    this.caturl = postterm[0].link;
    this.postname = postdata.title.rendered;
    this.postcontent = postdata.content.rendered;

    //下载链接
    this.downloadlist = postdata.exts.downloadList;
    //购买链接
    this.buyurls = postdata.exts.buyUrls;

    //文章更新记录
    this.postdate = postdata.date.split("T")[0];
    const modifieddate = postdata.modified.split("T")[0];
    this.activities[2].timestamp = this.postdate;
    this.activities[0].timestamp = modifieddate;
    this.activities[2].author = postdata.exts.authorName;
    this.activities[0].author = postdata.exts.modAuthorName;
    this.activities[1].content = '期间一共更新了' + postdata["_links"]["version-history"][0]["count"] + '次';
    this.thumbnail = postdata.exts.thumbnail;

    //文章标签
    this.taglist = postdata.exts.tagList;

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

    //侧边
  },
  computed: {
    editor: function() {
      return this.$refs.myQuillEditor.quill;
    },
  },
})
