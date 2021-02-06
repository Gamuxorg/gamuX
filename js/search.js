var category = new Vue({
  el: '#section',
  data: {
    postdata: [],
    siteurl: "",
    currentpage: 1,
    total: 0,
    thiscateid: 0,
    param: 0,
    formInline: {
      input: '',
    },
  },
  methods: {
    onSubmit: function() {
      axios({
        method: 'get',
        url: this.siteurl + '/wp-json/gamux/v1/search',
        headers: {
          "Access-Control-Expose-Headers" : "X-WP-Total",
        },
        params: {
          "per_page": 10,
          "search": this.formInline.input,
        },
      }).then(function(res){
        console.log(res["headers"]["x-wp-total"]);
//        this.total = res.header['X-WP-Total'];
//        this.postdata = res.data;
      });
    },
    handleCurrentChange: async function () {
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/gamux/v1/search',
        params: {
          "per_page": 10,
          "page": this.currentpage,
        },
      });
      this.postdata = a.data;
    },
  },
  mounted: async function() {
    this.siteurl = gamux.siteurl;

  },
})