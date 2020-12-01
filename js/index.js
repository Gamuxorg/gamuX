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
    getWishList: function(url, callback) {
      axios({
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
      }).then(function(response) {
          callback(response.data);
        });
    },
    getJsonComm: function(url, callback) {
      axios({
        method: 'get',
        url: url,
        responseType: 'json',
      }).then(function(response) {
          callback(response.data);
        });      
    }
  },
  mounted: function(){
    this.$nextTick(function(){
      this.carHeight = this.getCarHeight();
    });
    
    const that = this;
    window.onresize = function(){
      calTime1 = setTimeout(function(){
        that.carHeight = that.getCarHeight();
      }, 500);
    };

    that.getWishList('https://api.github.com/repos/Gamuxorg/bbs/issues',function(data){
      that.wishlist = data;
    });
    that.getJsonComm('wp-json/gamux/v1/images/mainslide/4', function(data){
      for(let k in data) {
        that.items[k] = {"src":"", "link":""};
        that.items[k]["src"] = data[k];
        console.log(data[k]);
      }
    });
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

