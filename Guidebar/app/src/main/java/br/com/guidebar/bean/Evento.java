package br.com.guidebar.bean;

import java.io.Serializable;

public class Evento implements Serializable {
	private static final long serialVersionUID = 1L;
	private Integer id;
	private Integer idadeMinima;
	private String nome;
	private String descricao;
	private String dataInicio;
	private String dataFim;
	private Boolean isAtivo;
	private Integer visualizacoes;
	private Categoria categoria;
	private Usuario promotor=new Usuario();
	private String latitude;
	private String longitude;
	private Float avaliacaoMedia;
	private Endereco endereco = new Endereco();
	private String thumbnail;
	private Boolean isOpenBar;
	private String filename;

	@Override
	public String toString() {
		return getNome();
	}

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public Integer getIdadeMinima() {
		return idadeMinima;
	}

	public void setIdadeMinima(Integer idadeMinima) {
		this.idadeMinima = idadeMinima;
	}

	public String getNome() {
		return nome;
	}

	public void setNome(String nome) {
		this.nome = nome;
	}

	public String getDescricao() {
		return descricao;
	}

	public void setDescricao(String descricao) {
		this.descricao = descricao;
	}

	public String getDataInicio() {
		return dataInicio;
	}

	public void setDataInicio(String dataInicio) {
		this.dataInicio = dataInicio;
	}

	public String getDataFim() {
		return dataFim;
	}

	public void setDataFim(String dataFim) {
		this.dataFim = dataFim;
	}

	public Boolean getIsAtivo() {
		return isAtivo;
	}

	public void setIsAtivo(Boolean isAtivo) {
		this.isAtivo = isAtivo;
	}

	public Integer getVisualizacoes() {
		return visualizacoes;
	}

	public void setVisualizacoes(Integer visualizacoes) {
		this.visualizacoes = visualizacoes;
	}

	public String getThumbnail() {
		return thumbnail;
	}

	public void setThumbnail(String thumbnail) {
		this.thumbnail = thumbnail;
	}

	public Categoria getCategoria() {
		return categoria;
	}

	public void setCategoria(Categoria categoria) {
		this.categoria = categoria;
	}

	public Usuario getPromotor() {
		return promotor;
	}

	public void setPromotor(Usuario promotor) {
		this.promotor = promotor;
	}

	public String getLatitude() {
		return latitude;
	}

	public void setLatitude(String latitude) {
		this.latitude = latitude;
	}

	public String getLongitude() {
		return longitude;
	}

	public void setLongitude(String longitude) {
		this.longitude = longitude;
	}

	public Float getAvaliacaoMedia() {
		return avaliacaoMedia;
	}

	public void setAvaliacaoMedia(Float avaliacaoMedia) {
		this.avaliacaoMedia = avaliacaoMedia;
	}

	public Endereco getEndereco() {
		return endereco;
	}

	public void setEndereco(Endereco endereco) {
		this.endereco = endereco;
	}

	public String getFilename() {
		return filename;
	}

	public void setFilename(String filename) {
		this.filename = filename;
	}

	public Boolean getIsOpenBar() {
		return isOpenBar;
	}

	public void setIsOpenBar(Boolean isOpenBar) {
		this.isOpenBar = isOpenBar;
	}

	
}
