wp.api.loadPromise.done( function() {

  // Create a new post
  var post = new wp.api.models.Post(
      {
        title: 'Posted via REST API',
        content: 'Lorem Ipsum ... ',
        status: 'draft',  // 'draft' is default, 'publish' to publish it
    }
  );

  var xhr = post.save( null, {
     success: function(model, response, options) {
       console.log(response);
     },
     error: function(model, response, options) {
       console.log(response);
     }
   });

});