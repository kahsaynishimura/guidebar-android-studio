package br.com.guidebar.bean;

import java.util.NoSuchElementException;

import android.widget.Adapter;

public class Produto {
	private Integer id;
	private Double price;
	private String name;
	private Integer quantity;
	private Integer availableQuantity;
	private String image;
	private String descricao;
	private Boolean isAtivo;
	private Evento evento = new Evento();
	private Integer idEvento;

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public Double getPrice() {
		return price;
	}

	public void setPrice(Double price) {
		this.price = price;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public Integer getQuantity() {
		return quantity;
	}

	public void setQuantity(Integer quantity) {
		this.quantity = quantity;
	}

	public String getImage() {
		return image;
	}

	public void setImage(String image) {
		this.image = image;
	}

	public String getDescricao() {
		return descricao;
	}

	public void setDescricao(String descricao) {
		this.descricao = descricao;
	}

	public Boolean getIsAtivo() {
		return isAtivo;
	}

	public void setIsAtivo(Boolean isAtivo) {
		this.isAtivo = isAtivo;
	}

	public Evento getEvento() {
		return evento;
	}

	public void setEvento(Evento evento) {
		this.evento = evento;
	}

	public Integer getIdEvento() {
		return idEvento;
	}

	public void setIdEvento(Integer idEvento) {
		this.idEvento = idEvento;
	}

	public static Integer getPositionById(final Adapter adapter,
			final Integer id) {
		Integer posicao = 0;
		try {
			final int count = adapter.getCount();
			for (int pos = 0; pos < count; pos++) {
				Produto i = (Produto) adapter.getItem(pos);

				if (id == (i.getId())) {
					posicao = pos;
				}
			}
		} catch (NoSuchElementException erro) {
			erro.printStackTrace();
		}
		return posicao;
	}

	public Integer getAvailableQuantity() {
		return availableQuantity;
	}

	public void setAvailableQuantity(Integer availableQuantity) {
		this.availableQuantity = availableQuantity;
	}
}
