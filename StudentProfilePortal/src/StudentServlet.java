import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

public class StudentServlet extends HttpServlet {

    @Override
    protected void doGet(
            HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);

        if (session == null ||
            session.getAttribute("username") == null) {

            response.sendRedirect(
                request.getContextPath() + "/login.jsp"
            );
            return;
        }

        List<Map<String, String>> students = new ArrayList<>();

        String sql =
            "SELECT id, name, department, email, image_path, document_path " +
            "FROM students ORDER BY id";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {

            while (rs.next()) {

                Map<String, String> student = new LinkedHashMap<>();

                student.put("id", rs.getString("id"));
                student.put("name", rs.getString("name"));
                student.put("department", rs.getString("department"));
                student.put("email", rs.getString("email"));
                student.put("image_path", rs.getString("image_path"));
                student.put("document_path", rs.getString("document_path"));

                students.add(student);
            }

            request.setAttribute("students", students);

            request.getRequestDispatcher("/students.jsp")
                   .forward(request, response);

        } catch (Exception e) {

            throw new ServletException(
                "Unable to retrieve student details from MySQL.", e
            );
        }
    }
}