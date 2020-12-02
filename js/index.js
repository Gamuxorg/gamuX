var index = new Vue({
  el: '#section',
  data: {
    items: [],
    carHeight: 0,
    wishlist: null,
  },
  methods: {
    getCarUrl: function(url) {
      return url;
    },
    getCarHeight: function() {
      return this.$refs.carcol1.$el.clientHeight;
    },
    getWishList: async function(url) {
      let a = await axios({
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
    getJsonComm: async function(url) {
      let a = await axios({
        method: 'get',
        url: url,
        responseType: 'json',
      });
      return a.data;
    }
  },
  mounted: async function(){
    this.$nextTick(function(){
      this.carHeight = this.getCarHeight();
    });

    let slidedata = await this.getJsonComm('wp-json/gamux/v1/images/mainslide/4');
    for(k in slidedata) {
      this.items[k] = {"value": 0, "src": "", "link": ""};
      this.items[k]["src"] = slidedata[k];
      this.items[k]["value"] = k;
    }
    console.log(slidedata);

    let wishlistdata = await this.getWishList('https://api.github.com/repos/Gamuxorg/bbs/issues');
    this.wishlist = wishlistdata;

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

