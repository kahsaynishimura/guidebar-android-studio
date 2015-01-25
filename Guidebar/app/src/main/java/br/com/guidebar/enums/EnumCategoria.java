package br.com.guidebar.enums;

import android.annotation.SuppressLint;
import android.util.SparseArray;

public class EnumCategoria {
	@SuppressLint("UseSparseArrays")
	public static enum ECategoria {
		Balada(1, "Balada"), Bar(2, "Bar"), Cervejada(3, "Cervejada"), Churrasco(
				4, "Churrasco"), Outra(5, "Outra");

		private Integer id;
		private String nome;
		private static SparseArray<ECategoria> relations;

		ECategoria(Integer id, String nome) {
			this.id = id;
			this.nome = nome;
		}

		public Integer getId() {
			return this.id;
		}

		public String getNome() {
			return this.nome;
		}

		public static ECategoria getCategoriaPorId(Integer id) {
			return relations.get(id);
		}

		static {
			relations = new SparseArray<ECategoria>();
			for (ECategoria p : values())
				relations.put(p.getId(), p);
		}

	}
}
