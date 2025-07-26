<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Application</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Submit Your Application</h2>
  <form action="submit.php" method="POST" enctype="multipart/form-data">
    <label>Full Name:
      <input type="text" name="full-name" required>
    </label><br>

    <label>Email:
      <input type="email" name="email" required>
    </label><br>

    <label>Position:
      <select name="position">
        <option>Frontend Developer</option>
        <option>Backend Developer</option>
        <option>Fullstack Developer</option>
      </select>
    </label><br>

    <label>Gender:
      <input type="radio" name="gender" value="Male"> Male
      <input type="radio" name="gender" value="Female"> Female
    </label><br>

    <label>Skills:
      <input type="checkbox" name="skills[]" value="HTML"> HTML
      <input type="checkbox" name="skills[]" value="CSS"> CSS
      <input type="checkbox" name="skills[]" value="JavaScript"> JavaScript
    </label><br>

    <label>Bio:
      <textarea name="bio" rows="4" cols="40"></textarea>
    </label><br>

    <label>Resume:
      <input type="file" name="resume">
    </label><br>

    <input type="submit" value="Submit Application">
  </form>
</body>
</html>
