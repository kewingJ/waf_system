/*
Un CRUD completo de GoLang y MySQL
*/
package main

import (
	// Leer líneas incluso si tienen espacios
	"database/sql" // Interactuar con bases de datos
	"fmt"          // Imprimir mensajes y esas cosas

	// El búfer, para leer desde la terminal con os.Stdin
	_ "github.com/go-sql-driver/mysql"
)

type BloqueoIp struct {
	ip_bloqueada, fecha_bloqueo_ip, tipo_ataque_ip, fecha_bloqueo_ip2 string
	Id                                                                int
}

type BloqueoWaf struct {
	ip, fecha_bloqueo, server, tipo_ataque, log_bloqueo string
	Id                                                  int
}

type VisitaDominio struct {
	ip_visita, dominio, fecha_visita string
	Id, total                        int
}

func obtenerBaseDeDatos() (db *sql.DB, e error) {
	usuario := "root"
	pass := ""
	host := "tcp(127.0.0.1:3306)"
	//nombreBaseDeDatos := "agenda"
	nombreBaseDeDatos := "waf"
	// Debe tener la forma usuario:contraseña@protocolo(host:puerto)/nombreBaseDeDatos
	db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@%s/%s", usuario, pass, host, nombreBaseDeDatos))
	if err != nil {
		return nil, err
	}
	return db, nil
}

func main() {
	creditos := `==========================================================
	            	MySQL y GO 
==========================================================`
	fmt.Println(creditos)
	menu := `¿Qué deseas hacer?
[1] -- Mostrar datos fuerza bruta
[2] -- Mostrar datos Waf
[3] -- Mostrar datos Visitas
[4] -- Salir
----->	`
	var eleccion int
	for eleccion != 4 {
		fmt.Print(menu)
		fmt.Scanln(&eleccion)
		switch eleccion {
		case 1:
			bloqueos, err := obtenerDatosFuerzaBruta()
			if err != nil {
				fmt.Printf("Error obteniendo datos fuerza bruta: %v", err)
			} else {
				for _, bloqueo := range bloqueos {
					fmt.Println("====================")
					fmt.Printf("Id: %d\n", bloqueo.Id)
					fmt.Printf("IP: %s\n", bloqueo.ip_bloqueada)
					fmt.Printf("Fecha 1: %s\n", bloqueo.fecha_bloqueo_ip)
					fmt.Printf("Tipo Ataque: %s\n", bloqueo.tipo_ataque_ip)
					fmt.Printf("Fecha 2: %s\n", bloqueo.fecha_bloqueo_ip2)
				}
			}
		case 2:
			bloqueos, err := obtenerDatosWaf()
			if err != nil {
				fmt.Printf("Error obteniendo datos waf: %v", err)
			} else {
				for _, bloqueo := range bloqueos {
					fmt.Println("====================")
					fmt.Printf("Id: %d\n", bloqueo.Id)
					fmt.Printf("IP: %s\n", bloqueo.ip)
					fmt.Printf("Fecha: %s\n", bloqueo.fecha_bloqueo)
					fmt.Printf("Server: %s\n", bloqueo.server)
					fmt.Printf("Tipo ataque: %s\n", bloqueo.tipo_ataque)
					fmt.Printf("Log: %s\n", bloqueo.log_bloqueo)
				}
			}
		case 3:
			visitas, err := obtenerDatosVisita()
			if err != nil {
				fmt.Printf("Error obteniendo datos visitas: %v", err)
			} else {
				for _, visita := range visitas {
					fmt.Println("====================")
					fmt.Printf("Id: %d\n", visita.Id)
					fmt.Printf("IP visita: %s\n", visita.ip_visita)
					fmt.Printf("Dominio: %s\n", visita.dominio)
					fmt.Printf("Total visita: %d\n", visita.total)
					fmt.Printf("Fecha Visita: %s\n", visita.fecha_visita)
				}
			}
		}
	}
}

func obtenerDatosFuerzaBruta() ([]BloqueoIp, error) {
	bloqueos := []BloqueoIp{}
	db, err := obtenerBaseDeDatos()
	if err != nil {
		return nil, err
	}
	defer db.Close()
	filas, err := db.Query("SELECT * FROM bloqueo_ip")

	if err != nil {
		return nil, err
	}
	// Si llegamos aquí, significa que no ocurrió ningún error
	defer filas.Close()

	// Aquí vamos a "mapear" lo que traiga la consulta en el while de más abajo
	var b BloqueoIp

	// Recorrer todas las filas, en un "while"
	for filas.Next() {
		err = filas.Scan(&b.Id, &b.ip_bloqueada, &b.fecha_bloqueo_ip, &b.tipo_ataque_ip, &b.fecha_bloqueo_ip2)
		// Al escanear puede haber un error
		if err != nil {
			return nil, err
		}
		// Y si no, entonces agregamos lo leído al arreglo
		bloqueos = append(bloqueos, b)
	}
	// Vacío o no, regresamos el arreglo de contactos
	return bloqueos, nil
}

func obtenerDatosWaf() ([]BloqueoWaf, error) {
	bloqueos := []BloqueoWaf{}
	db, err := obtenerBaseDeDatos()
	if err != nil {
		return nil, err
	}
	defer db.Close()
	filas, err := db.Query("SELECT id_bloqueo, ip, fecha_bloqueo, server, tipo_ataque, log_bloqueo FROM bloqueo")

	if err != nil {
		return nil, err
	}
	// Si llegamos aquí, significa que no ocurrió ningún error
	defer filas.Close()

	// Aquí vamos a "mapear" lo que traiga la consulta en el while de más abajo
	var b BloqueoWaf

	// Recorrer todas las filas, en un "while"
	for filas.Next() {
		err = filas.Scan(&b.Id, &b.ip, &b.fecha_bloqueo, &b.server, &b.tipo_ataque, &b.log_bloqueo)
		// Al escanear puede haber un error
		if err != nil {
			return nil, err
		}
		// Y si no, entonces agregamos lo leído al arreglo
		bloqueos = append(bloqueos, b)
	}
	// Vacío o no, regresamos el arreglo de contactos
	return bloqueos, nil
}

func obtenerDatosVisita() ([]VisitaDominio, error) {
	visitas := []VisitaDominio{}
	db, err := obtenerBaseDeDatos()
	if err != nil {
		return nil, err
	}
	defer db.Close()
	filas, err := db.Query("SELECT id_visita, ip_visita, dominio, COUNT(*) as total, fecha_visita FROM visita_dominio WHERE dominio <> '' AND activo_visita = 1 GROUP BY ip_visita")

	if err != nil {
		return nil, err
	}
	// Si llegamos aquí, significa que no ocurrió ningún error
	defer filas.Close()

	// Aquí vamos a "mapear" lo que traiga la consulta en el while de más abajo
	var b VisitaDominio

	// Recorrer todas las filas, en un "while"
	for filas.Next() {
		err = filas.Scan(&b.Id, &b.ip_visita, &b.dominio, &b.total, &b.fecha_visita)
		// Al escanear puede haber un error
		if err != nil {
			return nil, err
		}
		// Y si no, entonces agregamos lo leído al arreglo
		visitas = append(visitas, b)
	}
	// Vacío o no, regresamos el arreglo de contactos
	return visitas, nil
}
