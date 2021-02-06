var category = new Vue({
  el: '#section',
  data: {
    postdata: [],
    siteurl: "",
    currentpage: 1,
    total: 0,
    param: 0,
    formInline: {
      input: '',
    },
  },
  methods: {
    onSubmit: function() {
      window.location.href = this.siteurl + "/search?s=" + this.formInline.input;
    },
    handleCurrentChange: function () {
      const p = this.currentpage;
      window.location.href = this.siteurl + "/search?s=" + this.getQueryvalue('s') + "&p=" + p;
    },
    getQueryvalue: function(name) {
      var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
      var r = window.location.search.substr(1).match(reg);
      if( r != null ) {
        return  decodeURIComponent(r[2]);
      }
      return null;
    },
    getPostJson: async function() {
      const e = this.getQueryvalue('s');
      if (Number(this.getQueryvalue('p') == null)) {
        var currentpage = 1
      }
      else {
        var currentpage = Number(this.getQueryvalue('p'));
      }
      this.formInline.input = e;
      const a = await axios({
        method: 'get',
        url: this.siteurl + '/wp-json/gamux/v1/search',
        params: {
          "page": currentpage,
          "per_page": 10,
          "search": e,
        },
      });
      return a;
    }
  },
  mounted: async function() {
    this.siteurl = gamux.siteurl;
    const getJson = await this.getPostJson();
    this.total = Number(getJson["headers"]["x-wp-total"]);
    this.postdata = getJson.data.data;
    this.currentpage = this.getQueryvalue('p');
    this.formInline.input = decodeURIComponent(this.getQueryvalue('s'));
    console.log(getJson);
  },
})