package br.com.guidebar.bean;

import java.io.Serializable;

public class Imagem implements Serializable {
	private static final long serialVersionUID = 1L;
	private String caminho;
	private Integer id;
	private Evento evento = new Evento();

	public String getCaminho() {
		return caminho;
	}

	public void setCaminho(String caminho) {
		this.caminho = caminho;
	}

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public Evento getEvento() {
		return evento;
	}

	public void setEvento(Evento evento) {
		this.evento = evento;
	}

}