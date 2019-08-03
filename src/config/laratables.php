<?php

return [
    // Prefix for the DT_RowId value for each datatables record.
    'row_id_prefix' => 'laratables_row_',

    // Display format of dates (carbon instances) of the table.
    'date_format' => 'Y-m-d H:i:s',

    // Name of the columns that should not be searched for values in the datatables.
    'non_searchable_columns' => [],

    /*
     * Maximum number of records that can be fetched in a single API call.
     * As users of the site can update the value of the select option from the browser
     * and fetch unlimited records, we need to protect against that on the server side.
     *
     * Set this to 0 to allow all records to be fetched.
     **/
    'max_limit' => 0,
];
