package br.com.guidebar.bean;

import java.io.Serializable;

public class Participacao implements Serializable {
	private static final long serialVersionUID = 1L;
	private Integer id;
	private Integer evento;
	private Integer participante;
	private Boolean participando;
	private Usuario usuario;
	private Evento fkEvento;
	private String thumbnail;


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

	public Integer getParticipante() {
		return participante;
	}

	public void setParticipante(Integer participante) {
		this.participante = participante;
	}

	public Boolean getParticipando() {
		return participando;
	}

	public void setParticipando(Boolean participando) {
		this.participando = participando;
	}

	public static long getSerialversionuid() {
		return serialVersionUID;
	}

	
	public Usuario getUsuario() {
		return usuario;
	}

	public void setUsuario(Usuario usuario) {
		this.usuario = usuario;
	}

	public Evento getFkEvento() {
		return fkEvento;
	}

	public void setFkEvento(Evento fkEvento) {
		this.fkEvento = fkEvento;
	}

	public String getThumbnail() {
		return thumbnail;
	}

	public void setThumbnail(String thumbnail) {
		this.thumbnail = thumbnail;
	}


}
