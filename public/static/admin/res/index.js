/**
 * 项目JS主入口
 * 依赖Layui的layer和form模块为例
 * 2017-05-10 by yc
 * @return {[type]}
 */
layui.define(['layer', 'element', 'form'], function(exports) {
    var layer = layui.layer,
        form = layui.form();
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
                    if ($('#list').length) {
                        TF.getList($('#list'), res.url || TF.cache.list_url);
                    }
                }
                layer.msg(res.msg, {
                    time: 1000,
                    icon: 6
                });
            } else {
                var str = res.msg || '服务器异常';
                layer.msg(str, {
                    time: 1500,
                    icon: 5
                });
            }
        });
        return false;
    });

    /**
     * 全选，全不选
     */
    form.on('checkbox(checked-all)', function(data) {
        $(data.elem).closest('table').find('tr').find('td input[name="ids[]"]').each(function(index, el) {
            el.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

    /**
     * 异步获取列表数据并解析渲染
     * @param  view     jquery对象
     * @param  url      请求的url地址
     * @param  callback 回掉函数
     * @return {[type]} [dom]
     */
    TF.getList = function(view, url, callback) {
        var view = view || $('#list');
        var url = url || view.data('url') || view.attr('href');
        var isform = view.attr('form') || 0;
        if (!url) return;
        var param = view.closest('.content').find('form.search').serialize();
        $('#refresh').find('i').addClass('fa-spin');
        $.ajax({
            url: url,
            type: 'POST',
            data: param,
            dataType: 'html',
            success: function(html) {
                if (view.selector == '#list') {
                    TF.cache.list_url = url; //约定主列表页为#list
                }
                view.empty().html(html);
                if (isform) {
                    form.render();
                }
                if (typeof callback === 'function') {
                    callback();
                }
            },
            complete: function() {
                $('#refresh').find('i').removeClass('fa-spin');
            },
            error: function() {
                if (view.selector == '#list') {
                    view.empty().html('<p><i class="fa fa-warning"></i> 加载失败</p>');
                }

            }
        })
    }

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
            if ($(html).find('div').length == 0) {
                layer.msg(html);
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
                    var _form = $(layero).find('form');
                    $.post(_form.attr('action'), _form.serialize(), function(res) {
                        if (res.code == 1) {
                            if (target) {
                                window.location.href = res.url;
                            } else {
                                TF.getList($('#list'), res.url || TF.cache.list_url);
                            }
                            layer.msg(res.msg, {
                                time: 1000,
                                icon: 6
                            }, function() {
                                layer.close(index);
                            });
                        } else {
                            var str = res.msg || '服务器异常';
                            layer.msg(str, {
                                time: 1500,
                                icon: 5
                            });
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
                        TF.getList($('#list'), res.url || TF.cache.list_url);
                    }
                });
            });

        } else {
            $.get(url, function(res) {
                var message = self.attr('msg');
                if (message != 0 || res.code == 0) {
                    layer.msg(res.msg);
                }
                if (target) {
                    window.location.href = res.url;
                } else {
                    TF.getList($('#list'), res.url || TF.cache.list_url);
                }
            });
        }
        return false;
    })

    //快速排序
    $(document).on('change', '.input-sort', function(event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        if (self.val() != self.attr('data-val')) {
            $.post(url, self.serialize(), function(res) {
                if (res.code != 1) {
                    layer.msg(res.msg);
                    TF.getList($('#list'), res.url || TF.cache.list_url);
                } else {
                    self.attr('data-val', self.val());
                }
            });
        }
    });
    //快速排序
    $(document).on('mouseleave', '.input-sort', function(event) {
        $(this).change();
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
    
    //ajax-show
    $(document).on('click', '.iframe-info', function(event) {
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

    //刷新
    $('#refresh').click(function() {
        if ($('#list').length) {
            TF.getList('', TF.cache.list_url)
        } else {
            window.location.reload();
        }
    });

    //自动筛选
    $('.content').on('change', '.navbar-form select', function() {
        TF.getList();
    });
    $('.content').on('click', '#search', function() {
        var self = $(this);
        self.find('i').attr('class', 'fa fa-spin fa-spinner');
        TF.getList($('#list'), null, function() {
            self.find('i').attr('class', 'fa fa-search');
        });
        return false;
    });

    if ($('#list').length) {
        TF.getList($('#list'));
    }

    $(document).ajaxError(function() {
        layer.msg('服务器异常，请稍后再试!!!');
    });

    //分页
    $(document).on('click', '.pagination>li>a', function() {
        var href = $(this).attr('href');
        if (href && $(this).closest('#list').length) {
            TF.getList($('#list'), href);
        }
        return false;
    });

    TF.runUrl = function(self) {
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
                            TF.getList($('#list'), data.url);
                        }
                    }
                    layer.msg(data.msg, {
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
                        TF.getList($('#list'), data.url);
                    }
                }
                layer.msg(data.msg, {
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
                TF.runUrl(self);
                layer.close(index);
            });
        } else {
            TF.runUrl(self);
        }
        return false;
    });

    //注意，这里是模块输出的核心，模块名必须和use时的模块名一致
    exports('index', {});
});