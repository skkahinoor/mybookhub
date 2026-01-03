<!DOCTYPE html>
<html>
<head>
    <title>AJAX Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>AJAX Endpoints Test</h1>

    <div>
        <h2>Countries</h2>
        <select id="country-select">
            <option value="">Select Country</option>
        </select>
    </div>

    <div>
        <h2>States</h2>
        <select id="state-select">
            <option value="">Select State</option>
        </select>
    </div>

    <div>
        <h2>Districts</h2>
        <select id="district-select">
            <option value="">Select District</option>
        </select>
    </div>
    
    <div>
        <h2>Blocks</h2>
        <select id="block-select">
            <option value="">Select Block</option>
        </select>
    </div>

    <script>
    $(document).ready(function() {
        // Load countries
        $.ajax({
            url: '/institution-countries',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Countries:', response);
                var countrySelect = $('#country-select');
                countrySelect.empty();
                countrySelect.append('<option value="">Select Country</option>');

                $.each(response, function(key, value) {
                    countrySelect.append('<option value="' + key + '">' + value + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log('Error loading countries:', error);
                console.log('Response:', xhr.responseText);
            }
        });

        // Load states when country changes
        $('#country-select').on('change', function() {
            var countryId = $(this).val();

            if (!countryId) {
                $('#state-select').empty().append('<option value="">Select State</option>');
                $('#district-select').empty().append('<option value="">Select District</option>');
                $('#block-select').empty().append('<option value="">Select Block</option>');
                return;
            }

            $.ajax({
                url: '/institution-states',
                type: 'GET',
                data: { country: countryId },
                dataType: 'json',
                success: function(response) {
                    console.log('States:', response);
                    var stateSelect = $('#state-select');
                    stateSelect.empty();
                    stateSelect.append('<option value="">Select State</option>');

                    $.each(response, function(key, value) {
                        stateSelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Clear dependent dropdowns
                    $('#district-select').empty().append('<option value="">Select District</option>');
                    $('#block-select').empty().append('<option value="">Select Block</option>');
                },
                error: function(xhr, status, error) {
                    console.log('Error loading states:', error);
                    console.log('Response:', xhr.responseText);
                }
            });
        });

        // Load districts when state changes
        $('#state-select').on('change', function() {
            var stateId = $(this).val();

            if (!stateId) {
                $('#district-select').empty().append('<option value="">Select District</option>');
                $('#block-select').empty().append('<option value="">Select Block</option>');
                return;
            }

            $.ajax({
                url: '/institution-districts',
                type: 'GET',
                data: { state: stateId },
                dataType: 'json',
                success: function(response) {
                    console.log('Districts:', response);
                    var districtSelect = $('#district-select');
                    districtSelect.empty();
                    districtSelect.append('<option value="">Select District</option>');

                    $.each(response, function(key, value) {
                        districtSelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Clear dependent dropdowns
                    $('#block-select').empty().append('<option value="">Select Block</option>');
                },
                error: function(xhr, status, error) {
                    console.log('Error loading districts:', error);
                    console.log('Response:', xhr.responseText);
                }
            });
        });

        // Load blocks when district changes
        $('#district-select').on('change', function() {
            var districtId = $(this).val();

            if (!districtId) {
                $('#block-select').empty().append('<option value="">Select Block</option>');
                return;
            }

            $.ajax({
                url: '/institution-blocks',
                type: 'GET',
                data: { district: districtId },
                dataType: 'json',
                success: function(response) {
                    console.log('Blocks:', response);
                    var blockSelect = $('#block-select');
                    blockSelect.empty();
                    blockSelect.append('<option value="">Select Block</option>');

                    $.each(response, function(key, value) {
                        blockSelect.append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error loading blocks:', error);
                    console.log('Response:', xhr.responseText);
                }
            });
        });
    });
    </script>
</body>
</html>
