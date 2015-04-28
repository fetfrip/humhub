$.fn.isOnScreen = function(){
    var viewport = {};
    viewport.top = $(window).scrollTop();
    viewport.bottom = viewport.top + $(window).height();
    var bounds = {};
    bounds.top = this.offset().top;
    bounds.bottom = bounds.top + this.outerHeight();
    return ((bounds.top <= viewport.bottom) && (bounds.bottom >= viewport.top));
};

$(
    function () {

        function connect() {
            try {
                socket = new WebSocket("wss://fetfrip.com:8888");
                socket.onopen = connectionEstablished;
                socket.onmessage = newResponse;
                socket.onerror = onError;
            } catch (e) {
                console.log("could not connect ws");
            }
        
        }
        function newResponse(payload) {

            data = $.parseJSON(payload.data);
            if (data && data.command) {
                
                if (data.command == 'new_comments') {
                    if (data.comments.length == 0) return;
                    // Object to array
                    var sorted_comments = [];
                    for (i in data.comments) {
                        sorted_comments.push(data.comments[i]); 
                    };
                    sorted_comments.sort();

                    var area = $('#comments_area_Post_' + data.post);
                    var last_comment = area.find('div.media:last');
                    
                    if (last_comment.length > 0) {

                        var cid = (last_comment.attr('id')).match(/[0-9]*$/)[0];
                        for (i in sorted_comments){
                            var comment = $(sorted_comments[i]);
                            
                            if (comment) {
                                var id = ($(comment[0]).attr('id')).match(/[0-9]*$/)[0];
                                if (id > cid) {
                                    area.
                                        append($(comment[0]).hide()).
                                        append(comment[1]).
                                        append(comment[3]);
                                    $(comment[0]).slideDown();
                                    $(".time").timeago();
                                    initLikeModule();
                                }
                            }
                        }

                    } else {

                        for (i in sorted_comments){
                            var comment = $(sorted_comments[i]);
                            if (comment) {
                                area.parent().show();
                                area.
                                     append($(comment[0]).hide()).
                                     append(comment[1]).
                                     append(comment[3]);
                                $(comment[0]).slideDown();
                                $(".time").timeago();
                                initLikeModule();
                            }
                        }

                    }
                } else if (data.command == 'identify') {
                    socket.send(JSON.stringify({ command: 'identity', cookie: document.cookie, username: $('.user-title strong').text() })); 
                    commandPosts();
                } else if (data.command == 'posts') {
                    commandPosts();
                }
            }
        }
        function commandPosts() {
                    var posts = $('.post').map(function (i,e) {
                        return $(e).attr('id').match(/[0-9]*$/)[0];
                    });
                   
                    socket.send(JSON.stringify({ command: 'posts', posts: $.makeArray(posts) })); 
        }
        function connectionEstablished() {
            socket.send(JSON.stringify({ command: 'ready' }));
            initialized = true;
        }

        function onError() {
            //$('.notifications').append("err: rt off");
        }

        function checkComments() {
            if (typeof initialized == 'undefined') {
                connectionEstablished();
            }
            if (typeof document.hidden === 'undefined' || !document.hidden) {
                // we are on the tab or browser does not support visibility API
                // http://www.w3.org/TR/page-visibility/?csw=1#sec-document-interface
                var commands = "";
                $('div.post div.comment').each(function (i,e) {
                    var comments = $(e);
                    var container = comments.closest('div.wall-entry'); 
                    if (container.isOnScreen()) {
                        var post_id = (comments.attr("id")).match(/[0-9]*$/)[0];
                        var id = comments.find("div.content:last");
                        if (id.length) {
                            comment_id = (id.attr('id')).match(/[0-9]*$/)[0];
                        } else {
                            comment_id = -1;
                        }
                        socket.send(JSON.stringify({ command: "new_comments", post: post_id, comment: comment_id }));
                    }
                });
            }
        }
        if (typeof socket === 'undefined') {
            socket = "";
            connect();
            if (typeof check_comment_interval === 'undefined') {
                //check_comment_interval = setInterval(checkComments, 5000);
            }
        }
    }
)


