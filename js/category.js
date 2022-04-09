//全局变量一律大写，局部变量和函数内部变量一律小写，函数名驼峰
var category = new Vue({
    el: '#section',
    data: {
        //所有子分类信息
        CATEGORIES: [],
        //单个文章的信息
        POSTDATA: [],
        SITEURL: null,
        //当前页的码数
        CURRENTPAGE: null,
        //当前分类ID
        CURRENTID: null,
        //当前分类下的所有文章数
        PAGISHOW: false,
        CATSHOW: false,
        TOTAL: 0,
        //文章列表
        POSTLIST: [],
        CATID: null,
        PARAM: 0,
    },
    methods: {
        refresh: function() {
            window.location.href = this.SITEURL + "/gamelist";
        },
        queryParse: function() {
            var queryParse = location.href;
            try {
                queryParse = queryParse.replace("?", "?&").split("&");
                var queryParsec = [];
                queryParsec = {
                    "cat": Number((queryParse[1].split("="))[1]),
                    "page": Number((queryParse[2].split("="))[1])
                }
            } catch {
                queryParsec = {
                    "cat": 256,
                    "page": 1
                }
            }
            return queryParsec;
        },
        clickCat: async function(e) {
            this.CURRENTID = e.currentTarget.id;
            const a = await axios({
                method: 'get',
                url: this.SITEURL + '/wp-json/wp/v2/posts',
                params: {
                    "per_page": 20,
                    "categories": this.CURRENTID
                },
            });
            this.CURRENTPAGE = 1;
            this.POSTDATA = a.data;
            this.TOTAL = Number(a["headers"]["x-wp-total"]);
            window.history.pushState(null, null, this.SITEURL + "/gamelist?cat=" + this.CURRENTID + "&page=" + this.CURRENTPAGE);
        },
        handleCurrentChange: async function(val) {
            this.CURRENTPAGE = val;
            if (this.CURRENTID == 256) {
                var handleCurrentChange_param = {
                    "per_page": 20,
                    "include": this.POSTLIST.slice(20 * (this.CURRENTPAGE - 1), this.CURRENTPAGE * 20).join(',')
                }
            } else {
                var handleCurrentChange_param = {
                    "page": val,
                    "per_page": 20,
                    "categories": this.CURRENTID
                };
            }
            const a = await axios({
                method: 'get',
                url: this.SITEURL + '/wp-json/wp/v2/posts',
                params: handleCurrentChange_param,
            });
            this.POSTDATA = a.data;
            console.log(this.POSTDATA);
            window.history.pushState(null, null, this.SITEURL + "/gamelist?cat=" + this.CURRENTID + "&page=" + this.CURRENTPAGE);
        },
        //获取子分类信息
        getCatJson: async function() {
            const a = await axios({
                method: 'get',
                url: this.SITEURL + '/wp-json/wp/v2/categories',
                params: {
                    "per_page": 100,
                    "parent": 256
                },
            });
            return a.data;
        },
        //获取分类下所有文章的id
        getPostJson_gamux: async function() {
            const a = await axios({
                method: 'get',
                url: this.SITEURL + '/wp-json/gamux/v1/categories',
                params: {
                    "catid": this.CURRENTID
                },
            });
            //返回值是一个包含符合要求的所有文章id的字符串
            return a.data;
        },
        //初始化获取文章数据
        getPostJson: async function() {
            if (this.CURRENTID == 256) {
                var getPostJson_param = {
                    "per_page": 20,
                    "include": this.POSTLIST.slice(20 * (this.CURRENTPAGE - 1), this.CURRENTPAGE * 20).join(',')
                }
            } else {
                var getPostJson_param = {
                    "per_page": 20,
                    "categories": this.CURRENTID
                };
            }
            const a = await axios({
                method: 'get',
                url: this.SITEURL + '/wp-json/wp/v2/posts',
                params: getPostJson_param,
            });
            let b = a;
            return b;

        },
    },
    mounted: async function() {
        this.SITEURL = gamux.siteurl;

        this.CURRENTID = await this.queryParse().cat;
        this.CURRENTPAGE = await this.queryParse().page;
        this.CATEGORIES = await this.getCatJson();
        if(this.CURRENTID == 256) {
            catdata = await this.getPostJson_gamux();
            //json中获取的posts的值是一个字符串，需要转化为数组
            let postlistr = catdata.posts;
            this.POSTLIST = postlistr.split(",");
            //数组倒序，将新文章排在旧文章前面
            this.POSTLIST = this.POSTLIST.reverse();
            // 获取游戏的总数量
            this.TOTAL = catdata.count;
            let a = await this.getPostJson();
            this.POSTDATA = a.data;
        }
        
        if (this.CURRENTID != 256) {
            let a = await this.getPostJson();
            this.POSTDATA = a.data;
            console.log(a);
            this.TOTAL = Number(a["headers"]["x-wp-total"]);
        }
        this.$nextTick(async function() {
            this.CATSHOW = true;
            this.PAGISHOW = true;
        });
        console.log(this.POSTDATA);
    },

})