package br.com.guidebar.bean;

import java.io.Serializable;
import java.util.NoSuchElementException;

import android.widget.Adapter;

public class Categoria implements Serializable {
	private static final long serialVersionUID = 1L;
	private Integer id;
	private String nome;

	public Categoria() {
	}

	public Categoria(Integer id, String nome) {
		this.id = id;
		this.nome = nome;
	}

	public Categoria(String nome) {
		this.nome = nome;
	}

	public Categoria(int id) {
		this.id = id;
	}

	@Override
	public String toString() {
		return getNome();
	}

	public static Integer getPositionById(final Adapter adapter,
			final Integer id) {
		Integer posicao = 0;
		try {
			final int count = adapter.getCount();
			for (int pos = 0; pos < count; pos++) {
				Categoria c = (Categoria) adapter.getItem(pos);

				if (id == (c.getId())) {
					posicao = pos;
				}
			}
		} catch (NoSuchElementException erro) {
			erro.printStackTrace();
		}
		return posicao;
	}

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getNome() {
		return nome;
	}

	public void setNome(String nome) {
		this.nome = nome;
	}

}