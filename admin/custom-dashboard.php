<?php
/*
Template Name: Custom Dashboard
*/

get_header(); // Include your header
?>

<style>
    /* Custom CSS for macOS iOS style */
    body {
        font-family: "San Francisco", Arial, sans-serif;
        background-color: #f2f2f2;
    }

    .container {
        margin-top: 20px;
    }

    .table {
        background-color: #ffffff;
        border-radius: 6px;
    }

    .table thead {
        background-color: #0073e6;
        color: #ffffff;
    }

    .table th, .table td {
        padding: 10px;
    }

    h1 {
        color: #0073e6;
    }
</style>

<div id="primary" class="content-area container">
    <main id="main" class="site-main">
        <?php
        // Get the current user's email address
        $current_user = wp_get_current_user();
        $email = $current_user->user_email;

        // Query to get the school data
        $school_data = new WP_Query(array(
            'post_type' => 'schools', // Adjust to your post type name
            'posts_per_page' => -1,
        ));
        ?>

        <div class="container">
            <h1>Welcome, <?php echo $email; ?></h1>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Org ID</th>
                        <th>Staff Count</th>
                        <th>Child Count</th>
                        <th>Address</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($school_data->have_posts()) : $school_data->the_post();
                        $name = get_the_title();
                        $org_id = get_post_meta(get_the_ID(), 'org_id', true);
                        $staff_count = get_post_meta(get_the_ID(), 'staff_count', true);
                        $child_count = get_post_meta(get_the_ID(), 'child_count', true);
                        $address = get_post_meta(get_the_ID(), 'address', true);
                        $date_added = get_post_meta(get_the_ID(), 'date_added', true);
                    ?>
                        <tr>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $org_id; ?></td>
                            <td><?php echo $staff_count; ?></td>
                            <td><?php echo $child_count; ?></td>
                            <td><?php echo $address; ?></td>
                            <td><?php echo $date_added; ?></td>
                        </tr>
                    <?php endwhile;
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php get_footer(); // Include your footer