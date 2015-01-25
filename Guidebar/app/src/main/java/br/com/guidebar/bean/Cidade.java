package br.com.guidebar.bean;

import java.util.NoSuchElementException;

import android.widget.Adapter;

public class Cidade {
	private Integer id;
	private String nome;
	private Estado estado = new Estado();

	public Cidade(Integer id, String nome) {
		this.id = id;
		this.nome = nome;
	}

	public Cidade() {
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
				Cidade c = (Cidade) adapter.getItem(pos);

				if (id == (c.getId()).intValue()) {
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

	public Estado getEstado() {
		return estado;
	}

	public void setEstado(Estado estado) {
		this.estado = estado;
	}

}
