<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Export Booking Excel to Coprar Converter</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
  </head>
  <body>          
        
<div class="container">
    <div class="card" style="">
        <div class="card-body">
            <h5 class="card-title">Export Booking Excel to Coprar Converter</h5>
            <div class="form-group">
             
                <label for="recv_code">Receiver Code:</label><input class="form-control" type="text" id="recv_code" value="RECEIVER" name="recv_code" />
                <p><small>Please change before file select.</small></p>
            </div>
            <div class="form-group">
                <label for="recv_code">Callsign Code:</label><input class="form-control" type="text" id="callsign_code" value="XXXXX"  name="callsign_code"/>
                <p><small>Please change before file select.</small></p>
            </div>
            <div class="form-group">
                <label for="my_file_input">Export booking excel file:</label><input class="form-control" type="file" id="my_file_input" name="my_file_input" />
                <p><small><a href="sample.xlsx">Sample Excel</a></small></p>
            </div>

            <div class="form-group"><textarea class="form-control" rows="20" cols="40" id='my_file_output'>{{$edi}}</textarea></div>
        </div>
    </div>
</div>
</body>
</html>
