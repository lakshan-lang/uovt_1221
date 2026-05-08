<main class="container">
    <h2>Manage Medical Staff</h2>
    <link rel="stylesheet" href="style.css">
    <table class="data-table">
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Specialization</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through doctors from Database -->
            <tr>
                <td>Dr. Saman Kumara</td>
                <td>Cardiology</td>
                <td>0771234567</td>
                <td>
                    <a href="edit_doctor.php?id=1" class="btn-edit">Edit</a>
                    <a href="includes/deldoctor.php?id=1" class="btn-delete" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
            </tr>
        </tbody>
    </table>
</main>