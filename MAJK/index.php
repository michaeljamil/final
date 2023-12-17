<!DOCTYPE html>
<html lang="eng">
<head>
    <title>MAJK Employee Attendance System</title>
    <?php include('header.php') ?>
</head>
<body class="emp-dash">
    <div id="main" class="bg-dark">
        <div class="container-fluid admin2">
            <div class="button-container">
                <button class="adminbutton"><a href="admin/index.php">Admin Login</a></button>
            </div>

            <div class="attendance_log_field">
                <div id="company-logo-field" class="mb-4">
                    <h4 style="color: white;">MAJK Employee Attendance System</h4>
                </div>
                <div class="col-md-4 offset-md-4">
                    <div class="card">
                        <div class="card-title">
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h4><?php echo date('F d,Y') ?> <span id="now"></span></h4>
                            </div>
                            <div class="col-md-12">
                                <div class="text-center mb-4" id="log_display"></div>
                                <form action="" id="att-log-frm">
                                    <div class="form-group">
                                        <label for="eno" class="control-label">Enter your Employee ID </label>
                                        <input type="text" id="eno" name="eno" class="form-control col-sm-12">
                                    </div>
                                    <center>
                                        <button type="button" class='btn btn-sm btn-primary log_now col-sm-2' data-id="1">IN AM</button>
                                        <button type="button" class='btn btn-sm btn-danger log_now col-sm-2' data-id="2">OUT AM</button>
                                        <button type="button" class='btn btn-sm btn-primary log_now col-sm-2' data-id="3">IN PM</button>
                                        <button type="button" class='btn btn-sm btn-danger log_now col-sm-2' data-id="4">OUT PM</button>
                                    </center>
                                    <div class="loading" style="display: none"><center>Please wait...</center></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function () {
        setInterval(function () {
            var time = new Date();
            var now = time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true });

            // Split up the time components
            var timeComponents = now.split(/:| /);
            var hour = timeComponents[0];
            var minute = timeComponents[1];
            var second = timeComponents[2];
            var period = timeComponents[3];

            $('#now').html(now);
        }, 500);

        $('.log_now').click(function () {
            var _this = $(this);
            var eno = $('[name="eno"]').val();

            if (eno === '') {
                alert("Please enter your employee number");
                return;
            }

            $('.log_now').hide();
            $('.loading').show();

            // Split up the time components
            var time = new Date();
            var timeComponents = time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true }).split(/:| /);
            var hour = timeComponents[0];
            var minute = timeComponents[1];
            var second = timeComponents[2];
            var period = timeComponents[3];

            // Send time components along with other data
            $.ajax({
                url: './admin/time_log.php',
                method: 'POST',
                data: {
                    type: _this.attr('data-id'),
                    eno: $('[name="eno"]').val(),
                    hour: hour,
                    minute: minute,
                    second: second,
                    period: period
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    showError();
                },
                success: function (resp) {
                    console.log('Response from server:', resp);

                    try {
                        var result = JSON.parse(resp);

                        if (result.status === 1) {
                            $('[name="eno"]').val('');
                            $('#log_display').html(result.msg);
                        } else {
                            alert(result.msg);
                        }
                    } catch (e) {
                        console.log(e);
                        showError();
                    } finally {
                        // Show buttons and hide loading indicator
                        $('.log_now').show();
                        $('.loading').hide();

                        setTimeout(function () {
                            $('#log_display').html('');
                        }, 5000);
                    }
                }
            });
        });

        function showError() {
            alert("An error occurred. Please try again.");
            $('.log_now').show();
            $('.loading').hide();
        }
    });
</script>
</html>
