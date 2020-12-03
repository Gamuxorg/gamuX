var index = new Vue({
  el: '#section',
  data: {
    items: [],
    carHeight: 0,
    wishlist: null,
    postnum: 10,
    postdata: [],
  },
  methods: {
    getCarUrl: function(url) {
      return url;
    },
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
    getJsonComm: async function(url) {
      const a = await axios({
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
    const slidedata = await this.getJsonComm('wp-json/gamux/v1/images/mainslide/1');
    for(k=0;k<slidedata.length;k++) {
      this.items[k] = {"value": 0, "src": "", "link": ""};
      this.items[k]["src"] = slidedata[k]["imageSrc"];
      this.items[k]["link"] = slidedata[k]["postLink"];
      this.items[k]["value"] = Number(k);
      this.$set(this.items, k , {"value": k, "src": slidedata[k]["imageSrc"], "link": slidedata[k]["postLink"]});
    }

    const postdatas = await this.getJsonComm('wp-json/wp/v2/posts?per_page=10');
    for(i=0;i<postdatas.length;i++) {
      this.postdata[i] = postdatas[i];
      const a = this.postdata[i]["modified"];
      const b = a.split("T");
      const c = b[0];
      this.postdata[i]["modified"] = c;
    }
    console.log(postdatas);
    const wishlistdata = await this.getWishList('https://api.github.com/repos/Gamuxorg/bbs/issues');
    this.wishlist = wishlistdata;

    const that = this;
    window.onresize = function(){
      calTime1 = setTimeout(function(){
        that.carHeight = that.getCarHeight();
      }, 500);
    };
  },
  created: function() {
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

