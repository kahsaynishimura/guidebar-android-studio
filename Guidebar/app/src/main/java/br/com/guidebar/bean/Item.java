package br.com.guidebar.bean;

import java.io.Serializable;
import java.util.NoSuchElementException;

import android.widget.Adapter;

public class Item implements Serializable {

	private static final long serialVersionUID = 1L;
	private Integer id;
	private Integer quantity;
	private Double price;
	private Double subtotal;
	private Produto produto = new Produto();
	private Compra compra = new Compra();
	private Integer validated;

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public Integer getQuantity() {
		return quantity;
	}

	public void setQuantity(Integer quantity) {
		this.quantity = quantity;
	}

	public Double getPrice() {
		return price;
	}

	public void setPrice(Double price) {
		this.price = price;
	}

	public Double getSubtotal() {
		return subtotal;
	}

	public void setSubtotal(Double subtotal) {
		this.subtotal = subtotal;
	}

	public Produto getProduto() {
		return produto;
	}

	public void setProduto(Produto produto) {
		this.produto = produto;
	}

	public Compra getCompra() {
		return compra;
	}

	public void setCompra(Compra compra) {
		this.compra = compra;
	}

	public Integer getValidated() {
		return validated;
	}

	public void setValidated(Integer validated) {
		this.validated = validated;
	}

	public static Integer getPositionById(final Adapter adapter,
			final Integer id) {
		Integer posicao = 0;
		try {
			final int count = adapter.getCount();
			for (int pos = 0; pos < count; pos++) {
				Item i = (Item) adapter.getItem(pos);

				if (id == (i.getId())) {
					posicao = pos;
				}
			}
		} catch (NoSuchElementException erro) {
			erro.printStackTrace();
		}
		return posicao;
	}
}
