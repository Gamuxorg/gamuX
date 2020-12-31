var category = new Vue({
  el: '#section',
  data: {
    postdata: [],
    siteurl: "",
    currentpage: 1,
    total: 0,
    categories: [],
    cateidlist: [],
    thiscateid: 0,
    param: 0,
  },
  methods: {
    refresh: function() {
      location.reload(false);
    },
    clickCat: async function(e) {
      this.thiscateid = e.currentTarget.id;
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/wp/v2/posts',
        params: {
          "per_page": 20,
          "categories": [this.thiscateid],
        },
      });
      this.postdata = a.data;
      this.total = this.postdata.length;    
    },
    handleCurrentChange: async function (val) {
      if (this.thiscateid == 0) {
        var cat = this.cateidlist;
      }
      else {
        var cat = this.thiscateid;
      }
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/wp/v2/posts',
        params: {
          "per_page": 5,
          "page": val,
          "categories": cat,
        },
      });
      this.postdata = a.data;
    },
    getCatJson: async function() {
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/wp/v2/categories',
        params: {
          "parent": 255,
          "per_page": 100,
        },
      });
      return a.data;
    },
    getPostJson: async function() {
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/wp/v2/posts',
        params: {
          "per_page": 5,
          "categories": this.cateidlist,
        },
      });
      return a.data;
    },
  },
  mounted: async function() {
    this.siteurl = gamux.siteurl;
    const cat = await this.getCatJson();
    this.categories = cat;
    for (var i = 0; i < cat.length; i++) {
      this.cateidlist[i] = cat[i]['id'];
      this.total = this.total + cat[i]['count'];
    }
    this.$nextTick( async function() {
      this.postdata = await this.getPostJson();
    })

  },
})