<script>
Dcat.ready(function () {
    $(".backup-run").click(function() {
        var $btn = $(this);
        $btn.buttonLoading();
        $.ajax({
            url: $btn.attr('href'),
            method: 'POST',
            success: function (data) {
                if (data.status) {
                    $('.output-box').removeClass('d-none');
                    $('.output-box .output-body').html(data.message)
                }
                return false;
            },
            complete: function (xhr,status) {
                $btn.buttonLoading(false);
            }
        });
        return false;
    });

    $(".backup-delete").click(function() {
        let $btn = $(this);
        Dcat.confirm('确认要删除该备份数据吗？', null, function () {
            Dcat.loading({
                color: Dcat.color.primary,
            });
            $.ajax({
                url: $btn.attr('href'),
                method: 'DELETE',
                success: function (data){
                    Dcat.reload();
                    if (typeof data === 'object') {
                        if (data.status) {
                            Dcat.success(data.message);
                        } else {
                            Dcat.error(data.message);
                        }
                    }
                },
                complete: function (xhr,status) {
                    Dcat.loading(false);
                }
            }); 
            return false;
        });
    });
});
</script>

<style>
    .output-body {
        white-space: pre-wrap;
        background: #000000;
        color: #00fa4a;
        padding: 10px;
        border-radius: 0;
    }
    .todo-list>li .tools {
        display: none;
        float: none;
        color: #3c8dbc;
        margin-left: 10px;
    }

    .backup-delete {
        background: none;
        border: 0;
    }
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Existing backups</h3>

        <div class="box-tools">
            <button href="{{ route('backup-run') }}" class="btn btn-primary backup-run">Backup</button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Disk</th>
                <th>Reachable</th>
                <th>Healthy</th>
                <th># of backups</th>
                <th>Newest backup</th>
                <th>Used storage</th>
            </tr>
            @foreach($backups as $index => $backup)
            <tr data-toggle="collapse" data-target="#trace-{{$index+1}}" style="cursor: pointer;">
                <td>{{ $index+1 }}.</td>
                <td>{{ @$backup[0] }}</td>
                <td><span class="badge badge-primary">{{ @$backup['disk'] }}</span></td>
                <td>{{ @$backup[1] }}</td>
                <td>{{ @$backup[2] }}</td>
                <td>{{ @$backup['amount'] }}</td>
                <td>{{ @$backup['newest'] }}</td>
                <td>{{ @$backup['usedStorage'] }}</td>
            </tr>
            <tr class="collapse" id="trace-{{$index+1}}">
                <td colspan="8">
                    <ul class="todo-list ui-sortable">
                        @foreach($backup['files'] as $file)
                        <li>
                            <span class="text">{{ $file }}</span>
                            <!-- Emphasis label -->

                            <div class="tools">
                                <a target="_blank" href="{{ route('backup-download', ['disk' => $backup['disk'], 'file' => $backup[0].'/'.$file]) }}"><i class="fa fa-download"></i></a>
                                <button href="{{ route('backup-delete', ['disk' => $backup['disk'], 'file' => $backup[0].'/'.$file]) }}" class="backup-delete text-primary"><i class="fa fa-trash-o"></i></button>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endforeach

            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div>

<div class="box box-default output-box d-none">
    <div class="box-header with-border">
        <i class="fa fa-terminal"></i>

        <h3 class="box-title">Output</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <pre class="output-body"></pre>
    </div>
    <!-- /.box-body -->
</div>