package br.com.guidebar.bean;

import java.util.NoSuchElementException;

import android.widget.Adapter;

public class Estado {
	private Integer id;
	private String nome;

	public Estado(Integer id, String nome) {
		this.id = id;
		this.nome = nome;
	}

	public Estado() {
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
				Estado c = (Estado) adapter.getItem(pos);

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
