var category = new Vue({
  el: '#section',
  data: {
    postdata: [],
    siteurl: "",
    currentpage: 1,
    total: 0,
    categories: 0,
  },
  methods: {
    handleCurrentChange:  function(val) {
      this.$nextTick( async function() {
        if (this.categories == 0) {
          this.getPostJson(val);
        }
        else {
          this.getPostJson(val, this.categories);
        }
      });
    },
    getCatJson: async function () {
      let a = await axios({
        method: 'get',
        url: this.siteurl + "/wp-json/wp/v2/categories",
        params: {
          parent: 112,
        },
        responseType: 'json',
      });
      this.total = 0;
      this.categories = a.data;
      for (let i = 0; i < a.data.length; i++) {
        this.total = this.total + (a.data)[i].count;
      }
    },
    clickCatJson: async function (e) {
      var par = "/" + e.currentTarget.id;
      this.categories = e.currentTarget.id;
      let a = await axios({
        method: 'get',
        url: this.siteurl + "/wp-json/wp/v2/categories" + par,
        params: {
          parent: 112,
        },
        responseType: 'json',
      });
      this.total = a.data.count;
    },
    getPostJson: async function(page,cate) {
      let a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/wp/v2/posts',
        params: {
          per_page: 20,
          page: page,
          categories: cate,
        },
        responseType: 'json',
      });
      this.postdata = a.data;
    },
  },
  mounted: function() {
    this.siteurl = gamux.siteurl;
    this.$nextTick( async function() {
      await this.getCatJson();
      await this.getPostJson(1);
    })

  },
})