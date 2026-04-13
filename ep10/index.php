<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Management API</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <style>
:root {
  --primary: #00f5ff;
  --secondary: #a855f7;
  --bg-dark: #020617;
  --glass: rgba(255,255,255,0.05);
  --border: rgba(255,255,255,0.1);
  --text: #e2e8f0;
}

/* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Body */
body {
  font-family: 'Outfit', sans-serif;
  background: radial-gradient(circle at 20% 20%, #1e293b, #020617);
  color: var(--text);
  min-height: 100vh;
  padding: 40px 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Container */
.container {
  width: 100%;
  max-width: 1100px;
  padding: 35px;
  border-radius: 25px;

  background: rgba(255,255,255,0.05);
  backdrop-filter: blur(25px);
  border: 1px solid var(--border);

  box-shadow: 
    0 0 40px rgba(0,255,255,0.2),
    0 0 80px rgba(168,85,247,0.15);
}

/* Heading */
h2 {
  text-align: center;
  font-size: 32px;
  margin-bottom: 30px;

  background: linear-gradient(90deg, #00f5ff, #a855f7);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Controls */
.controls {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 30px;
}

/* Inputs */
.controls input,
.controls select {
  padding: 12px 18px;
  border-radius: 12px;
  border: 1px solid var(--border);
  background: rgba(0,0,0,0.4);
  color: white;
  font-size: 14px;
  transition: 0.3s;
}

.controls input:focus,
.controls select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 10px var(--primary);
}

/* Button */
.controls button {
  padding: 12px 20px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #00f5ff, #a855f7);
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

.controls button:hover {
  transform: scale(1.08);
  box-shadow: 0 0 15px #00f5ff;
}

/* Table */
.table-responsive {
  overflow-x: auto;
  border-radius: 15px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

/* Header */
thead {
  background: rgba(255,255,255,0.05);
}

thead th {
  padding: 15px;
  text-transform: uppercase;
  font-size: 13px;
  color: #94a3b8;
}

/* Rows */
tbody tr {
  transition: 0.3s;
}

tbody tr:hover {
  background: rgba(0,255,255,0.05);
  transform: scale(1.01);
}

/* Cells */
tbody td {
  padding: 15px;
  font-size: 15px;
}

/* Badge */
.badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
}

/* Course Colors */
.course-CS {
  background: rgba(0,255,255,0.2);
  color: #00f5ff;
}
.course-IT {
  background: rgba(168,85,247,0.2);
  color: #a855f7;
}
.course-SE {
  background: rgba(255,0,150,0.2);
  color: #ff4da6;
}
.course-ECE {
  background: rgba(255,200,0,0.2);
  color: #ffcc00;
}
.course-ME {
  background: rgba(0,255,150,0.2);
  color: #00ffa6;
}

/* No result */
.no-result {
  text-align: center;
  padding: 30px;
  color: #94a3b8;
}

/* Smooth animation */
tbody tr {
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}
</style>
</head>
<body>

  <div class="container">
    <h2>Student Management API</h2>

    <div class="controls">
      <input type="text" id="searchInput" placeholder="Search student name..." oninput="renderTable()"/>
      <select id="courseFilter" onchange="renderTable()">
        <option value="All">All Courses</option>
        <option value="CS">CS</option>
        <option value="IT">IT</option>
        <option value="SE">SE</option>
        <option value="ECE">ECE</option>
        <option value="ME">ME</option>
      </select>
      <button onclick="sortByMarks()">Sort by Marks</button>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Marks</th>
            <th>Course</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
  </div>

  <script>
    let sortDesc = false;

    function sortByMarks() {
      sortDesc = !sortDesc;
      renderTable();
    }

    async function renderTable() {
      const search = document.getElementById("searchInput").value;
      const course = document.getElementById("courseFilter").value;

      const url = `api.php?search=${encodeURIComponent(search)}&course=${encodeURIComponent(course)}&sortDesc=${sortDesc}`;

      try {
        const response = await fetch(url);
        const filtered = await response.json();
        const tbody = document.getElementById("tableBody");

        if (filtered.length === 0) {
          tbody.innerHTML = `<tr><td colspan="5" class="no-result">No students found matching your criteria.</td></tr>`;
          return;
        }

        tbody.innerHTML = filtered.map(s => {
          const badgeClass = `badge course-${s.course}`;
          return `
            <tr>
              <td>#${s.id}</td>
              <td style="font-weight: 500;">${s.name}</td>
              <td>${s.age} yrs</td>
              <td style="font-weight: 600;">${s.marks}</td>
              <td><span class="${badgeClass}">${s.course}</span></td>
            </tr>
          `;
        }).join('');
      } catch (err) {
        console.error("Error fetching students:", err);
      }
    }

    // Initial render
    renderTable();
  </script>

</body>
</html>