package br.com.guidebar.bean;

import java.io.Serializable;

public class Favorito  implements Serializable {
	private static final long serialVersionUID = 1L;
	private Integer id;
	private Integer evento;
	private Integer assinante;
	private Boolean assinou;
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
	public Integer getAssinante() {
		return assinante;
	}
	public void setAssinante(Integer assinante) {
		this.assinante = assinante;
	}
	public Boolean getAssinou() {
		return assinou;
	}
	public void setAssinou(Boolean assinou) {
		this.assinou = assinou;
	}
	public static long getSerialversionuid() {
		return serialVersionUID;
	}
	
}
