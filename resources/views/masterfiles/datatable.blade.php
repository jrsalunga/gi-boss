<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel DataTables Tutorial</title>

        <!-- Bootstrap CSS -->
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="//editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.1/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            body {
                padding-top: 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="table-resposive">
            <table class="table table-bordered" id="users-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Descriptor</th>
                        <th>Company</th>
                        <th>Open Date</th>
                        <th>Close Date</th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>

        <!-- jQuery -->
        <script src="//code.jquery.com/jquery.js"></script>
        <!-- DataTables -->
        <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
        <script src="//editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
        <!-- Bootstrap JavaScript -->
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <!-- App scripts -->
        <script>
        $(function() {
            $('#users-table').DataTable({
                                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '/api/m/branch',
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'descriptor', name: 'descriptor' },
                    { data: 'company.descriptor', name: 'company', orderable: true, searchable: false},
                    { data: 'opendate', name: 'opendate' },
                    { data: 'closedate', name: 'closedate' }
                ],
                select: true,

            });
        });
        </script>
    </body>
</html>