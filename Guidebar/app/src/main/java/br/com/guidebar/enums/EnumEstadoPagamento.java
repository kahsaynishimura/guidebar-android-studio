package br.com.guidebar.enums;

import android.annotation.SuppressLint;
import android.util.SparseArray;

public class EnumEstadoPagamento {
	@SuppressLint("UseSparseArrays")
	public static enum EstadoPagamento {
		AguardandoPagamento(1, "Aguardando Pagamento"), EmAnalise(2,
				"Em Análise"), Pago(3, "Pago"), Disponivel(4, "Disponível"), EmDisputa(
				5, "Em Disputa"), Devolvida(6, "Devolvida"), Cancelada(7,
				"Cancelada"), NaoPago(8,"Não Pago");

		private Integer id;
		private String nome;
		private static SparseArray<EstadoPagamento> relations;

		EstadoPagamento(Integer id, String nome) {
			this.id = id;
			this.nome = nome;
		}

		public Integer getId() {
			return this.id;
		}

		public String getNome() {
			return this.nome;
		}

		public static EstadoPagamento getEstadoPagamentoPorId(Integer isPago) {
			return relations.get(isPago);
		}

		static {
			relations = new SparseArray<EstadoPagamento>();
			for (EstadoPagamento p : values())
				relations.put(p.getId(), p);
		}

		
	}
}
