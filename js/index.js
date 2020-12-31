var index = new Vue({
  el: '#section',
  data: {
    slidedata: [],
    carHeight: 0,
    wishlist: null,
    postnum: 10,
    postdata: [],
    caturl: "",
    siteurl: "",
    cateidlist: [],
  },
  methods: {
    getCarHeight: function() {
      return this.$refs.carcol1.$el.clientHeight;
    },
    getWishList: async function(url) {
      const a = await axios({
        method: 'get',
        url: url,
        responseType: 'json',
        params: {
          accept: 'application/vnd.github.v3+json',
          filter: 'all',
          state: 'open',
          per_page: 10,
          page: 1,
          labels: '请求游戏',
        },
      });
      return a.data;
    },
    getCatJson: async function() {
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/wp/v2/categories',
        params: {
          "parent": 256,
          "per_page": 100,
        },
      });
      return a.data;
    },
    getJsonComm: async function(url) {
      const a = await axios({
        method: 'get',
        url: url,
        params: {
          "per_page": 12,
          "page": 1,
          "categories": this.cateidlist,
        }
      });
      return a.data;
    }
  },
  mounted: async function(){
    this.siteurl = gamux.siteurl;
    this.$nextTick(function(){
      this.carHeight = this.getCarHeight();
    });
    //首页轮播
    const cate = await this.getCatJson();
    for (var i = 0; i < cate.length; i++) {
      this.cateidlist[i] = cate[i]["id"];
    }
    const slidedatas = await this.getJsonComm(this.siteurl + '/wp-json/gamux/v1/images/mainslide/4');
    this.slidedata = slidedatas.data;
    //游戏上新
    this.postdata = await this.getJsonComm(this.siteurl + 'wp-json/wp/v2/posts');
    //需求清单
    const wishlistdata = await this.getWishList('https://api.github.com/repos/Gamuxorg/bbs/issues');
    this.wishlist = wishlistdata;
    //文章高度与轮播高度一致
    const that = this;
    window.onresize = function(){
      calTime1 = setTimeout(function(){
        that.carHeight = that.getCarHeight();
      }, 500);
    };
  },
  watch: {
    carHeight: function(val){
      this.carHeight = val;
    },
  },
  beforeDestory: function() {
    clearTimeout(calTime1);
  }
})

