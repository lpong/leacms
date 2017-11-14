$(function() {
    jQuery.fn.getList = function(_url, callback) {
        $('#refresh').find('i').addClass('fa-spin');
        var that = this;
        var url = _url || that.attr('data-url') || '';
        var isform = that.attr('form') || 0;
        if (!url) return false;
        var param = that.siblings('.navbar').find('form').serialize();
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'html',
                data: param,
            })
            .done(function(html) {
                that.empty().html(html);
                if (isform) {
                    layui.use('form', function() {
                        form.render();
                    });
                }
                lea.cache_list_url = url;
            })
            .fail(function() {
                that.html('<p><i class="fa fa-warning"></i> 服务器异常，请稍后再试~</p>');
            })
            .always(function() {
                $('#refresh').find('i').removeClass('fa-spin');
                if (typeof callback === 'function') {
                    callback();
                }
            });
    };
    if ($('.ajax-list').length) {
        $('.ajax-list').each(function(index, el) {
            $(this).getList();
        });
    }
});

layui.use(['layer', 'element', 'form', 'laydate'], function() {
    /**
     * msg格式化
     */
    var lea2msg = function(msg) {
        var _msg = '';
        if (typeof msg === 'object') {
            $.each(msg, function(i, val) {
                _msg += '<li style="text-align:left;list-syle-type:square">' + val + '</li>';
            });
        } else {
            _msg = msg;
        }
        return _msg;
    }

    var layer = layui.layer,
        form = layui.form,
        element = layui.element,
        laydate = layui.laydate;

    laydate.render({
        elem: '.laydate-range',
        type: 'date',
        range: '~'
    });


    //监听提交
    form.on('submit(layform)', function(data) {
        var target = $(data.elem).attr('target') || 0;
        $.post(data.form.action, data.field, function(res) {
            if (res.code == 1) {
                if (target) {
                    if (res.url) {
                        window.location.href = res.url;
                    } else {
                        window.location.reload();
                    }
                } else {
                    if ($(data.elem).closest('.ajax-list').length) {
                        $(data.elem).closest('.ajax-list').getList(lea.cache_list_url);
                    }
                }
                layer.msg(res.msg, {
                    time: 1000,
                    icon: 6
                });
            } else {
                layer.msg(lea2msg(res.msg), {
                    time: 1500,
                    icon: 5
                });
            }
        });
        return false;
    });

    $(document).on('click', '.ajax-submit', function(event) {
        event.preventDefault();
        var self = $(this);
        self.attr('disabled', 'disabled');
        $.post(self.attr('action'), self.closest('form').serialize(), function(data) {
            layer.msg(lea2msg(data.msg), function() {
                if (data.url) {
                    window.location.href = data.url;
                }
            });
            self.removeAttr('disabled');
        });
        return false;
    });

    //快速排序
    $(document).on('change', '.input-sort', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        if (self.val() != self.attr('data-val')) {
            $.post(url, self.serialize(), function(res) {
                if (res.code != 1) {
                    layer.msg(lea2msg(res.msg));
                    self.closest('.ajax-list').getList(res.url || lea.cache_list_url);
                } else {
                    self.attr('data-val', self.val());
                }
            });
        }
    });

    //自动筛选
    $(document).on('change', '.navbar-form select', function() {
        $(this).closest('.navbar').next('.ajax-list').getList();
    });
    $(document).on('click', '.search', function(event) {
        event.preventDefault();
        var son = $(this).find('i');
        son.attr('class', 'fa fa-spin fa-spinner')
        son.closest('.navbar').next('.ajax-list').getList('', function() {
            son.attr('class', 'fa fa-search');
        });
        return false;
    });

    document.onkeydown = function(event) {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 27) { // 按 Esc 
            //要做的事情
        }
        if (e && e.keyCode == 113) { // 按 F2 
            //要做的事情
        }
        if (e && e.keyCode == 13) { // enter 键
            $('.search-form').last().click();
        }
    };

    /**
     * 异步获取表单
     * 异步提交表单
     * 表单验证
     */
    $(document).on('click', '.ajax-form', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        if (!url) return;
        var target = self.attr('target') || '';
        $.get(url, function(html) {
            if (typeof html === 'object') {
                layer.msg(html.msg);
                return false;
            }
            layer.open({
                type: 1,
                title: self.attr('title'),
                content: html,
                scrollbar: false,
                maxWidth: '80%',
                btn: ['确定', '取消'],
                yes: function(index, layero) {
                    if ($(layero).find('.layui-layer-btn0').attr('disabled')) {
                        return false;
                    }
                    $(layero).find('.layui-layer-btn0').attr('disabled', 'disabled');
                    var _form = $(layero).find('form');
                    $.post(_form.attr('action'), _form.serialize(), function(res) {
                        if (res.code == 1) {
                            if (target) {
                                window.location.href = res.url;
                            } else {
                                if (self.closest('.ajax-list').length) {
                                    self.closest('.ajax-list').getList(lea.cache_list_url);
                                } else {
                                    self.closest('.navbar').next('.ajax-list').getList(lea.cache_list_url);
                                }
                            }
                            layer.msg(lea2msg(res.msg), {
                                time: 1000,
                                icon: 6
                            }, function() {
                                layer.close(index);
                            });
                        } else {
                            var str = lea2msg(res.msg) || '服务器异常';
                            layer.msg(str, {
                                time: 2000,
                                icon: 5
                            });
                            $(layero).find('.layui-layer-btn0').removeAttr('disabled')
                        }

                    }, 'json');
                },
                btn2: function(index) {
                    layer.close(index);
                },
                success: function() {
                    form.render();
                }
            }, 'html');
        });
        return false;
    });

    /**
     * 异步url请求
     * 用户简单操作，如删除
     */
    $(document).on('click', '.ajax-get', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        var title = self.attr('title') || '执行该操作';
        var target = self.attr('target') || '';
        if (!url) return false;

        if (self.attr('confirm')) {
            layer.confirm('您确定要 <span style="color:#f56954">' + title + '</span> 吗？', function(index) {
                $.get(url, function(res) {
                    layer.msg(res.msg);
                    if (target) {
                        window.location.href = res.url;
                    } else {
                        self.closest('.ajax-list').getList(lea.cache_list_url);
                    }
                });
            });

        } else {
            $.get(url, function(res) {
                var message = self.attr('msg');
                if (message != 0 && res.code == 0) {
                    layer.msg(res.msg);
                }
                if (target) {
                    window.location.href = res.url;
                } else {
                    self.closest('.ajax-list').getList(lea.cache_list_url);
                }
            });
        }
        return false;
    })

    lea.runUrl = function(self) {
        var url = self.attr('href');
        if (self.attr('message') == 1) {
            layer.prompt({
                formType: 2,
                title: '请输入原因',
                value: self.attr('placeholder')
            }, function(value, index, elem) {
                $.post(url, 'message=' + value, function(data) {
                    layer.close(index);
                    if (data.code == 1) {
                        if (self.attr('target') == 1) {
                            if (data.url) {
                                window.location.href = data.url;
                            }
                        } else {
                            self.closest('.ajax-list').getList(lea.cache_list_url);
                        }
                    }
                    layer.msg(lea2msg(data.msg), {
                        time: 1000
                    });
                });
                layer.close(index);
            });
        } else {
            $.post(url, function(data) {
                if (data.code == 1) {
                    if (self.attr('target') == 1) {
                        if (data.url) {
                            window.location.href = data.url;
                        }
                    } else {
                        self.closest('.ajax-list').getList(lea.cache_list_url);
                    }
                }
                layer.msg(lea2msg(data.msg), {
                    time: 1000
                });

            });
        }
    }

    //审核操作
    $(document).on('click', '.audit', function(event) {
        event.preventDefault();
        var self = $(this);
        var confirm = self.attr('confirm') || 0;
        if (confirm == 1) {
            layer.confirm('您确定要 <span style="color:red">' + self.attr('title') || self.text() + '</span> 吗？', function(index) {
                lea.runUrl(self);
                layer.close(index);
            });
        } else {
            lea.runUrl(self);
        }
        return false;
    });

    //刷新
    $('#refresh').click(function() {
        var self = $(this);
        self.addClass('fa-spin');
        if ($('.ajax-list').length) {
            $('.ajax-list').getList(lea.cache_list_url)
        } else {
            window.location.reload();
        }
    });

    //分页
    $(document).on('click', '.pagination>li>a', function() {
        var href = $(this).attr('href');
        if (href && $(this).closest('.ajax-list').length) {
            $(this).closest('.ajax-list').getList(href);
            return false;
        }
    });


    //ajax-show
    $(document).on('click', '.ajax-show', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        var title = self.attr('data-title') || self.attr('title') || false;
        $.get(url, function(data) {
            layer.open({
                type: 1,
                title: title,
                shade: 0.8,
                area: ['85%', '95%'],
                //id: 'ajax-show',
                moveType: 1,
                offset: '20px',
                scrollbar: true,
                content: data
            });
        });
        return false;
    });
    $(document).on('click', '.ajax-info', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        var title = self.attr('data-title') || self.attr('title') || false;
        $.get(url, function(data) {
            layer.open({
                type: 1,
                title: title,
                shade: 0.8,
                moveType: 1,
                maxWidth: '80%',
                offset: '20px',
                scrollbar: true,
                content: data
            });
        });
        return false;
    });
    //ajax-show
    $(document).on('click', '.iframe', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        var title = self.attr('title') || false;
        var width = self.data('width') || '80%';
        console.log(width);
        layer.open({
            type: 2,
            title: title,
            shade: 0.8,
            area: [width, '95%'],
            id: 'ajax-show',
            moveType: 1,
            offset: '20px',
            scrollbar: true,
            content: url
        });
        return false;
    });

    $(document).on('dblclick', '.layui-tab-title>li', function() {
        $(this).closest('.layui-tab').find('.layui-show').find('.ajax-list').getList();
    });

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (xhr.status != 200) {
            layer.msg('服务器异常，请稍后再试~');
        }
        if (xhr.status == 302) {
            window.location.reload();
        }
    });
});