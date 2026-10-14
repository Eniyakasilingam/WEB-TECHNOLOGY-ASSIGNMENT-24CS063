import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

public class FileServlet extends HttpServlet {

    @Override
    protected void doGet(
            HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);

        if (session == null ||
            session.getAttribute("username") == null) {

            response.sendError(
                HttpServletResponse.SC_UNAUTHORIZED,
                "Please sign in to access student files."
            );
            return;
        }

        String pathInfo = request.getPathInfo();

        if (pathInfo == null || pathInfo.length() < 2) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }

        String fileName = pathInfo.substring(1);

        if (!fileName.matches("[a-zA-Z0-9._-]+")) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }

        String uploadPath =
            getServletContext().getRealPath("/uploads");

        if (uploadPath == null) {
            response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            return;
        }

        File uploadDirectory = new File(uploadPath).getCanonicalFile();
        File file = new File(uploadDirectory, fileName).getCanonicalFile();

        if (!uploadDirectory.equals(file.getParentFile())) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }

        if (!file.isFile() || !file.canRead()) {
            response.sendError(
                HttpServletResponse.SC_NOT_FOUND,
                "Student file not found."
            );
            return;
        }

        String contentType =
            getServletContext().getMimeType(fileName);

        if (contentType == null) {
            contentType = "application/octet-stream";
        }

        response.setContentType(contentType);
        response.setContentLengthLong(file.length());

        if (contentType.startsWith("image/")) {
            response.setHeader("Content-Disposition", "inline");
        } else {
            response.setHeader(
                "Content-Disposition",
                "attachment; filename=\"" + fileName + "\""
            );
        }

        try (FileInputStream input = new FileInputStream(file);
             OutputStream output = response.getOutputStream()) {

            byte[] buffer = new byte[8192];
            int bytesRead;

            while ((bytesRead = input.read(buffer)) != -1) {
                output.write(buffer, 0, bytesRead);
            }

            output.flush();
        }
    }
}