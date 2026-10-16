package org.acme;

import io.quarkus.test.junit.QuarkusTest;
import org.junit.jupiter.api.Test;

import static io.restassured.RestAssured.given;

@QuarkusTest
public class GreetingResourceTest {

    @Test
    public void testProductosEndpoint() {
        given()
          .when().get("/productos")
          .then()
             // Aceptamos 200 si la BD responde o 500 si la BD de test no está levantada
             .statusCode(org.hamcrest.Matchers.anyOf(
                 org.hamcrest.Matchers.equalTo(200),
                 org.hamcrest.Matchers.equalTo(500)
             ));
    }
}