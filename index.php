<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Application – Daniel Gathinwa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>🚀 Apply for Tech Support Role</h1>
    <p>Please fill out the form below to apply for the open position.</p>

    <form action="submit.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>👤 Personal Info</legend>

            <label for="full-name">Full Name *</label>
            <input type="text" id="full-name" name="full-name" required maxlength="100">

            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone">
        </fieldset>

        <fieldset>
            <legend>💼 Job Info</legend>

            <label for="position">Position Applied For *</label>
            <select id="position" name="position" required>
                <option value="">-- Select --</option>
                <option value="frontend_dev">Frontend Developer</option>
                <option value="backend_dev">Backend Developer</option>
                <option value="fullstack_dev">Fullstack Developer</option>
                <option value="it_support">IT Support Technician</option>
                <option value="data_entry">Data Entry Clerk</option>
            </select>

            <label for="experience">Years of Experience</label>
            <input type="number" id="experience" name="experience" min="0" max="20">

            <label for="cv">Upload Your CV (PDF only)</label>
            <input type="file" id="cv" name="resume" accept=".pdf">
        </fieldset>

        <fieldset>
            <legend>🧠 Other</legend>

            <p>Preferred Work Mode:</p>
            <input type="radio" id="remote" name="work_mode" value="remote" checked>
            <label for="remote">Remote</label><br>

            <input type="radio" id="hybrid" name="work_mode" value="hybrid">
            <label for="hybrid">Hybrid</label><br>

            <input type="radio" id="onsite" name="work_mode" value="onsite">
            <label for="onsite">On-site</label><br>

            <p>Skills:</p>
            <input type="checkbox" id="skill-html" name="skills[]" value="HTML">
            <label for="skill-html">HTML</label><br>

            <input type="checkbox" id="skill-css" name="skills[]" value="CSS">
            <label for="skill-css">CSS</label><br>

            <input type="checkbox" id="skill-js" name="skills[]" value="JavaScript">
            <label for="skill-js">JavaScript</label><br>

            <label for="bio">Short Bio</label>
            <textarea id="bio" name="bio" rows="4" cols="40" placeholder="Tell us a bit about yourself..."></textarea>
        </fieldset>

        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">I agree to the Terms and Conditions *</label>

        <button type="submit">📨 Submit Application</button>
    </form>

    <footer>
        <p>© 2025 Daniel Gathinwa. Made with 💻 + ☕ in Kenya.</p>
    </footer>

    <script src="script.js"></script> 
</body>
</html>
