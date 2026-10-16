package org.acme.repositories;

import org.acme.models.Producto;
import jakarta.enterprise.context.ApplicationScoped;
import jakarta.transaction.Transactional;
import javax.sql.DataSource;
import jakarta.inject.Inject;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

@ApplicationScoped
public class ProductoRepository {

    @Inject
    DataSource dataSource;

    public List<Producto> listar() {
        List<Producto> productos = new ArrayList<>();
        // Filtramos para mostrar solo los productos activos (eliminado = 0)
        String sql = "SELECT id, nombre, proveedor, fecha_vencimiento, precio, stock, COALESCE(eliminado, 0) FROM producto WHERE COALESCE(eliminado, 0) = 0";

        try (Connection conn = dataSource.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                Producto p = new Producto();
                p.setId(rs.getLong("id"));
                p.setNombre(rs.getString("nombre"));
                p.setProveedor(rs.getString("proveedor"));
                p.setFecha_vencimiento(rs.getString("fecha_vencimiento"));
                p.setPrecio(rs.getBigDecimal("precio"));
                p.setStock(rs.getInt("stock"));
                p.setEliminado(rs.getInt(7));
                productos.add(p);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return productos;
    }

    @Transactional
    public Long guardar(Producto p) throws SQLException {
        String sql = "INSERT INTO producto (nombre, proveedor, fecha_vencimiento, precio, stock, eliminado) VALUES (?, ?, ?, ?, ?, ?) RETURNING id";
        Long generatedId = null;

        try (Connection conn = dataSource.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
             
            pstmt.setString(1, p.getNombre());
            pstmt.setString(2, p.getProveedor());
            
            // Validar fecha de vencimiento opcional
            if (p.getFecha_vencimiento() != null && !p.getFecha_vencimiento().trim().isEmpty()) {
                pstmt.setDate(3, java.sql.Date.valueOf(p.getFecha_vencimiento()));
            } else {
                pstmt.setNull(3, Types.DATE);
            }

            pstmt.setBigDecimal(4, p.getPrecio());
            pstmt.setInt(5, p.getStock() != null ? p.getStock() : 0);
            pstmt.setInt(6, p.getEliminado() != null ? p.getEliminado() : 0);
            
            System.out.println("--- EJECUTANDO INSERT EN REPOSITORIO ---");
            try (ResultSet rs = pstmt.executeQuery()) {
                if (rs.next()) {
                    generatedId = rs.getLong(1);
                    p.setId(generatedId);
                    System.out.println(">>> ID GENERADO CAPTURADO: " + generatedId);
                }
            }
        } catch (SQLException e) {
            System.out.println(">>> ERROR SQL DETECTADO:");
            e.printStackTrace();
            throw e;
        }

        return generatedId;
    }

    @Transactional
    public boolean actualizar(Long id, Producto p) {
        String sql = "UPDATE producto SET nombre = ?, proveedor = ?, fecha_vencimiento = ?, precio = ?, stock = ? WHERE id = ?";
        
        try (Connection conn = dataSource.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
             
            pstmt.setString(1, p.getNombre());
            pstmt.setString(2, p.getProveedor());
            
            if (p.getFecha_vencimiento() != null && !p.getFecha_vencimiento().trim().isEmpty()) {
                pstmt.setDate(3, java.sql.Date.valueOf(p.getFecha_vencimiento()));
            } else {
                pstmt.setNull(3, Types.DATE);
            }

            pstmt.setBigDecimal(4, p.getPrecio());
            pstmt.setInt(5, p.getStock() != null ? p.getStock() : 0);
            pstmt.setLong(6, id);
            
            int filasAfectadas = pstmt.executeUpdate();
            return filasAfectadas > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }

    @Transactional
    public boolean eliminarLogico(Long id) {
        // Actualiza el campo eliminado a 1 en lugar de borrar el registro físicamente
        String sql = "UPDATE producto SET eliminado = 1 WHERE id = ?";
        
        try (Connection conn = dataSource.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
             
            pstmt.setLong(1, id);
            int filasAfectadas = pstmt.executeUpdate();
            return filasAfectadas > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }
}