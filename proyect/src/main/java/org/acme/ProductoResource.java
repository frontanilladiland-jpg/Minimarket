package org.acme;

import org.acme.models.Producto;
import org.acme.repositories.ProductoRepository;

import jakarta.inject.Inject;
import jakarta.ws.rs.*;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;
import java.util.List;

@Path("/productos")
@Produces(MediaType.APPLICATION_JSON)
@Consumes(MediaType.APPLICATION_JSON)
public class ProductoResource {

    @Inject
    ProductoRepository productoRepository;

    @GET
    public List<Producto> listar() {
        return productoRepository.listar();
    }

    @POST
    public Response guardar(Producto p) {
        try {
            Long id = productoRepository.guardar(p);
            if (id != null) {
                p.setId(id);
                return Response.status(Response.Status.CREATED).entity(p).build();
            }
            return Response.status(Response.Status.BAD_REQUEST)
                     .entity("{\"error\": \"No se pudo registrar el producto\"}")
                     .build();
        } catch (Exception e) {
            e.printStackTrace();
            return Response.status(Response.Status.INTERNAL_SERVER_ERROR)
                     .entity("{\"error\": \"" + e.getMessage() + "\"}")
                     .build();
        }
    }

    @PUT
    @Path("/{id}")
    public Response actualizar(@PathParam("id") Long id, Producto p) {
        boolean actualizado = productoRepository.actualizar(id, p);
        if (actualizado) {
            return Response.ok(p).build();
        }
        return Response.status(Response.Status.NOT_FOUND)
                 .entity("{\"error\": \"Producto no encontrado para actualizar\"}")
                 .build();
    }

    @DELETE
    @Path("/{id}")
    public Response eliminarLogico(@PathParam("id") Long id) {
        boolean eliminado = productoRepository.eliminarLogico(id);
        if (eliminado) {
            return Response.ok("{\"mensaje\": \"Producto eliminado lógicamente con éxito\"}").build();
        }
        return Response.status(Response.Status.NOT_FOUND)
                 .entity("{\"error\": \"Producto no encontrado\"}")
                 .build();
    }
}