package com.asw.modulo_administracion_vehiculos;

import org.junit.jupiter.api.Test;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.test.context.ActiveProfiles;

import com.asw.modulo_administracion_vehiculos.service.NatsService;

@SpringBootTest(classes = ModuloAdministracionVehiculosApplication.class)
@ActiveProfiles(" test")

class ModuloAdministracionVehiculosApplicationTests {

	@MockBean
	private NatsService natsService;

	@Test
	void contextLoads() {
	}

}
