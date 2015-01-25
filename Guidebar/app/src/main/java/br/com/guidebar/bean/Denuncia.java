package br.com.guidebar.bean;

import java.io.Serializable;

public class Denuncia implements Serializable {
	private static final long serialVersionUID = 1L;
	private Integer id;
	private Integer evento;
	private Integer usuario;
	private String data;
	
	public Integer getId() {
		return id;
	}
	public void setId(Integer id) {
		this.id = id;
	}
	public Integer getEvento() {
		return evento;
	}
	public void setEvento(Integer evento) {
		this.evento = evento;
	}
	public Integer getUsuario() {
		return usuario;
	}
	public void setUsuario(Integer usuario) {
		this.usuario = usuario;
	}
	public String getData() {
		return data;
	}
	public void setData(String data) {
		this.data = data;
	}
	
}
