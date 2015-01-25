package br.com.guidebar.bean;

import java.io.Serializable;

public class Avaliacao implements Serializable {
	private static final long serialVersionUID = 1L;
	private Integer id;
	private Integer eventId;
	private Integer userId;
	private Float valor;

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

    public static long getSerialVersionUID() {
        return serialVersionUID;
    }

    public Integer getEventId() {
        return eventId;
    }

    public void setEventId(Integer eventId) {
        this.eventId = eventId;
    }

    public Integer getUserId() {
        return userId;
    }

    public void setUserId(Integer userId) {
        this.userId = userId;
    }

    public Float getValor() {
		return valor;
	}

	public void setValor(Float valor) {
		this.valor = valor;
	}



}
