<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<%@ page import="java.util.List" %>
<%@ page import="java.util.Map" %>

<%!
    private String escapeHtml(String value) {
        if (value == null) return "";

        return value.replace("&", "&amp;")
                    .replace("<", "&lt;")
                    .replace(">", "&gt;")
                    .replace("\"", "&quot;")
                    .replace("'", "&#39;");
    }

    private String fileNameOnly(String path) {
        if (path == null) return "";

        String normalized = path.replace('\\', '/');
        int index = normalized.lastIndexOf('/');

        return index >= 0
            ? normalized.substring(index + 1)
            : normalized;
    }
%>

<%
    if (session.getAttribute("username") == null) {
        response.sendRedirect(
            request.getContextPath() + "/login.jsp"
        );
        return;
    }

    List<Map<String, String>> students =
        (List<Map<String, String>>) request.getAttribute("students");

    if (students == null) {
        response.sendRedirect(request.getContextPath() + "/students");
        return;
    }
%>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student Portal | Profiles</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 35px 6%;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #e5e7eb;
            background:
                radial-gradient(ellipse at top left, #102e20, transparent 42%),
                radial-gradient(ellipse at bottom right, #10281e, transparent 40%),
                #080d0a;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 45px;
        }

        .brand {
            font-size: 23px;
            font-weight: 800;
        }

        .brand span {
            color: #4ade80;
        }

        .logout {
            padding: 12px 20px;
            border: 1px solid #28543a;
            border-radius: 12px;
            color: #4ade80;
            text-decoration: none;
        }

        .logout:hover {
            background: #153024;
        }

        .tag {
            color: #4ade80;
            font-size: 13px;
            letter-spacing: 5px;
        }

        h1 {
            font-size: clamp(30px, 4vw, 46px);
            margin-bottom: 18px;
        }

        .subtitle {
            color: #9ca3af;
            margin-bottom: 42px;
            font-size: 17px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 25px;
        }

        .card {
            min-width: 0;
            padding: 30px;
            border: 1px solid rgba(74, 222, 128, .20);
            border-radius: 25px;
            background: rgba(255, 255, 255, .045);
            backdrop-filter: blur(18px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, .20);
            transition: .25s;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(74, 222, 128, .65);
        }

        .avatar {
            position: relative;
            width: 95px;
            height: 95px;
            display: grid;
            place-items: center;
            margin-bottom: 28px;
            border: 1px solid rgba(74, 222, 128, .22);
            border-radius: 25px;
            background: rgba(34, 197, 94, .12);
            color: #4ade80;
            font-size: 36px;
            font-weight: 800;
            overflow: hidden;
        }

        .avatar img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card h2 {
            margin: 0 0 18px;
            font-size: 25px;
        }

        .department {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 12px;
            background: rgba(34, 197, 94, .12);
            color: #4ade80;
            font-size: 13px;
        }

        .email {
            margin: 30px 0;
            color: #a1aab8;
            overflow-wrap: anywhere;
        }

        .document-link {
            display: inline-block;
            color: #4ade80;
            font-weight: 700;
            text-decoration: none;
            line-height: 1.8;
        }

        .document-link:hover {
            color: #86efac;
            text-decoration: underline;
        }

        .empty {
            padding: 25px;
            border: 1px solid #28543a;
            border-radius: 15px;
            color: #9ca3af;
        }

        @media (max-width: 1000px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            body {
                padding: 25px 5%;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="brand">Student<span>Portal</span></div>

        <a class="logout" href="<%= request.getContextPath() %>/logout.jsp">
            Sign Out &rarr;
        </a>
    </nav>

    <main>
        <p class="tag">STUDENT MANAGEMENT</p>

        <h1>Student Profiles</h1>

        <p class="subtitle">
            View student information and access their uploaded documents.
        </p>

        <% if (students.isEmpty()) { %>

            <div class="empty">No student records were found.</div>

        <% } else { %>

            <section class="cards">

                <% for (Map<String, String> student : students) {

                    String name = student.get("name");
                    String image = fileNameOnly(student.get("image_path"));
                    String document = fileNameOnly(student.get("document_path"));

                    String imageUrl = request.getContextPath()
                        + "/files/" + image;

                    String documentUrl = request.getContextPath()
                        + "/files/" + document;
                %>

                    <article class="card">

                        <div class="avatar">
                            <%= name != null && !name.isEmpty()
                                ? escapeHtml(name.substring(0, 1))
                                : "S" %>

                            <% if (!image.isEmpty()) { %>

                                <img
                                    src="<%= escapeHtml(imageUrl) %>"
                                    alt="Student photo"
                                    onerror="this.style.display='none'">

                            <% } %>
                        </div>

                        <h2><%= escapeHtml(name) %></h2>

                        <span class="department">
                            <%= escapeHtml(student.get("department")) %>
                        </span>

                        <p class="email">
                            <%= escapeHtml(student.get("email")) %>
                        </p>

                        <% if (!document.isEmpty()) { %>

                            <a class="document-link"
                               href="<%= escapeHtml(documentUrl) %>">
                                View Student Document &rarr;
                            </a>

                        <% } else { %>

                            <span class="email">No document available.</span>

                        <% } %>

                    </article>

                <% } %>

            </section>

        <% } %>

    </main>

</body>
</html>